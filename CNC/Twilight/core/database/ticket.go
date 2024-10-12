package database

import (
    "time"
)

//ticket stuct
type Ticket struct {
    ID       int64     `json:"id"`
    UserID   int     `json:"user_id"`
    Title    string    `json:"title"`
    Message  string    `json:"message"`
    Response string    `json:"response"`
    Status   string    `json:"status"`
    Date     int64     `json:"date"`
    Username string `json:"username"`
    CreatedAt time.Time `json:"created_at"`
}
//message stuct
type Message struct {
    ID             int64     `json:"id"`
    TicketID       int64     `json:"ticket_id"`
    UserID         int64     `json:"user_id"`
    Message        string    `json:"message"`
    CreatedAt      time.Time `json:"created_at"`
}

//new tickets for user
func (conn *Instance) NewTicket(userID int, title, message string) error {
    if userID == 0 || title == "" || message == "" {
        return ErrInvalidInput
    }
    status := "open"
    
    stmt, err := conn.conn.Prepare("INSERT INTO `tickets` (`user_id`, `title`, `status`) VALUES (?, ?, ?)")
    if err != nil {
        logger.Println("database/NewTicket(): error occurred while preparing statement:", err)
        return err
    }
    defer stmt.Close()
    res, err := stmt.Exec(userID, title, status)
    if err != nil {
        logger.Println("database/NewTicket(): error occurred while executing statement:", err)
        return err
    }
    
    ticketID, err := res.LastInsertId()
    if err != nil {
        logger.Println("database/NewTicket(): error occurred while getting last insert ID:", err)
        return err
    }


    stmtMsg, err := conn.conn.Prepare("INSERT INTO `messages` (`ticket_id`, `user_id`, `message`) VALUES (?, ?, ?)")
    if err != nil {
        logger.Println("database/NewTicket(): error occurred while preparing message statement:", err)
        return err
    }
    defer stmtMsg.Close()
    _, err = stmtMsg.Exec(ticketID, userID, message)
    if err != nil {
        logger.Println("database/NewTicket(): error occurred while executing message statement:", err)
        return err
    }

    return nil
}

//get tickets for users
func (conn *Instance) GetTickets(user *User) ([]*Ticket, error) {
    var tickets []*Ticket


    stmt, err := conn.conn.Prepare("SELECT `id`, `title`, `status`, `created_at` FROM `tickets` WHERE `user_id` = ?")
    if err != nil {
        logger.Println("database/GetTicketsForUser: error occurred while preparing statement:", err)
        return nil, err
    }
    defer stmt.Close()


    rows, err := stmt.Query(user.ID)
    if err != nil {
        logger.Println("database/GetTicketsForUser: error occurred while executing query:", err)
        return nil, err
    }
    defer rows.Close()


    for rows.Next() {
        var ticket Ticket
        err := rows.Scan(&ticket.ID, &ticket.Title, &ticket.Status, &ticket.CreatedAt)
        if err != nil {
            logger.Println("database/GetTicketsForUser: error occurred while scanning row:", err)
            continue 
        }
        
        tickets = append(tickets, &ticket)
    }


    if err := rows.Err(); err != nil {
        logger.Println("database/GetTicketsForUser: error occurred during iteration:", err)
        return nil, err
    }

    return tickets, nil
}

// Get all tickets
func (conn *Instance) GetAllTickets() ([]*Ticket, error) {
    var tickets []*Ticket

    stmt, err := conn.conn.Prepare("SELECT `id`, `user_id`, `title`, `status`, `created_at` FROM `tickets`")
    if err != nil {
        logger.Println("database/GetAllTickets: error occurred while preparing statement:", err)
        return nil, err
    }
    defer stmt.Close()

    rows, err := stmt.Query()
    if err != nil {
        logger.Println("database/GetAllTickets: error occurred while executing query:", err)
        return nil, err
    }
    defer rows.Close()

    for rows.Next() {
        var ticket Ticket
        err := rows.Scan(&ticket.ID, &ticket.UserID, &ticket.Title, &ticket.Status, &ticket.CreatedAt)
        if err != nil {
            logger.Println("database/GetAllTickets: error occurred while scanning row:", err)
            continue 
        }
        
        tickets = append(tickets, &ticket)
    }

    if err := rows.Err(); err != nil {
        logger.Println("database/GetAllTickets: error occurred during iteration:", err)
        return nil, err
    }

    return tickets, nil
}

//get tickets w the id for ticket info
func (conn *Instance) GetTicketByID(ticketID int64) (*Ticket, error) {

    var ticket Ticket
    stmtTicket, err := conn.conn.Prepare("SELECT `user_id`, `title`, `status`, `created_at` FROM `tickets` WHERE `id` = ?")
    if err != nil {
        logger.Println("database/GetTicketByID: error occurred while preparing ticket statement:", err)
        return nil, err
    }
    defer stmtTicket.Close()

    rowTicket := stmtTicket.QueryRow(ticketID)
    err = rowTicket.Scan(&ticket.UserID, &ticket.Title, &ticket.Status, &ticket.CreatedAt)
    if err != nil {
        logger.Println("database/GetTicketByID: error occurred while scanning ticket row:", err)
        return nil, err
    }

    stmtMessages, err := conn.conn.Prepare("SELECT `user_id`, `message`, `created_at` FROM `messages` WHERE `ticket_id` = ?")
    if err != nil {
        logger.Println("database/GetTicketByID: error occurred while preparing message statement:", err)
        return nil, err
    }
    defer stmtMessages.Close()

    rowsMessages, err := stmtMessages.Query(ticketID)
    if err != nil {
        logger.Println("database/GetTicketByID: error occurred while querying messages:", err)
        return nil, err
    }
    defer rowsMessages.Close()

    for rowsMessages.Next() {
        var msg Message
        err := rowsMessages.Scan(&msg.UserID, &msg.Message, &msg.CreatedAt)
        if err != nil {
            logger.Println("database/GetTicketByID: error occurred while scanning message row:", err)
            continue
        }
    }

    if err := rowsMessages.Err(); err != nil {
        logger.Println("database/GetTicketByID: error occurred during iteration of messages:", err)
        return nil, err
    }

    return &ticket, nil
}

// updates the message content for a specific ticket and user
func (conn *Instance) UpdateMessage(ticketID int64, userID int, message string) error {
    if ticketID == 0 || userID == 0 || message == "" {
        return ErrInvalidInput
    }

    stmt, err := conn.conn.Prepare("INSERT INTO `messages` (`ticket_id`, `user_id`, `message`) VALUES (?, ?, ?)")
    if err != nil {
        logger.Println("database/UpdateMessage: error occurred while preparing statement:", err)
        return err
    }
    defer stmt.Close()

    _, err = stmt.Exec(ticketID, userID, message)
    if err != nil {
        logger.Println("database/UpdateMessage: error occurred while executing statement:", err)
        return err
    }

    return nil
}

// updates the message content for a specific ticket and user
func (conn *Instance) UpdateTicket(ticketID int64, status string) error {
    if ticketID == 0 || status == "" {
        return ErrInvalidInput
    }

    stmt, err := conn.conn.Prepare("UPDATE `tickets` SET `status` = ? WHERE `id` = ?")
    if err != nil {
        logger.Println("database/UpdateMessage: error occurred while preparing statement:", err)
        return err
    }
    defer stmt.Close()

    _, err = stmt.Exec(status, ticketID)
    if err != nil {
        logger.Println("database/UpdateMessage: error occurred while executing statement:", err)
        return err
    }

    return nil
}

func (conn *Instance) GetMessagesByTicketID(ticketID int64) ([]*Message, error) {
    var messages []*Message

    stmt, err := conn.conn.Prepare("SELECT `id`, `ticket_id`, `user_id`, `message`, `created_at` FROM `messages` WHERE `ticket_id` = ?")
    if err != nil {
        logger.Println("database/GetMessagesByTicketID: error occurred while preparing statement:", err)
        return nil, err
    }
    defer stmt.Close()

    rows, err := stmt.Query(ticketID)
    if err != nil {
        logger.Println("database/GetMessagesByTicketID: error occurred while executing query:", err)
        return nil, err
    }
    defer rows.Close()

    for rows.Next() {
        var message Message
        err := rows.Scan(&message.ID, &message.TicketID, &message.UserID, &message.Message, &message.CreatedAt)
        if err != nil {
            logger.Println("database/GetMessagesByTicketID: error occurred while scanning row:", err)
            continue
        }
        messages = append(messages, &message)
    }

    if err := rows.Err(); err != nil {
        logger.Println("database/GetMessagesByTicketID: error occurred during iteration:", err)
        return nil, err
    }

    return messages, nil
}

func (conn *Instance) DeleteTicketByID(ticketID int64) error {
    if ticketID == 0 {
        return ErrInvalidInput
    }

    // Delete messages associated with the ticket
    _, err := conn.conn.Exec("DELETE FROM `messages` WHERE `ticket_id` = ?", ticketID)
    if err != nil {
        logger.Println("database/DeleteTicketByID: error occurred while deleting messages:", err)
        return err
    }

    // Delete the ticket
    _, err = conn.conn.Exec("DELETE FROM `tickets` WHERE `id` = ?", ticketID)
    if err != nil {
        logger.Println("database/DeleteTicketByID: error occurred while deleting ticket:", err)
        return err
    }

    return nil
}