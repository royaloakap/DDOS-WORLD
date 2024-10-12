ulimit -n 1024000
xvfb-run -a node --optimize_for_size --always_compact sakura.js $@
