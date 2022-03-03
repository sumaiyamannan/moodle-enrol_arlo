# Moodle Tool Beacon

Plugin used to beacon moodle information to Fleet Tracker

## Branches

LMS                | Branch           | PHP
------------------ | ---------------- | ----
Moodle 3.2 - 3.7   | master           | 5.6
Moodle 3.8+        | MOODLE_38_STABLE | 7.4+
Totara 9 - 12      | master           | 5.6
Totara 13+         | MOODLE_38_STABLE | 7.4+


## Configuration

- beaconbaseurl can be set in the GUI, but secretkey can only be set through config.php
- Easiest to set both through forced_plugin_settings

```
$CFG->forced_plugin_settings['tool_beacon']['secretkey'] = 'super-secret-key';
$CFG->forced_plugin_settings['tool_beacon']['beaconbaseurl'] = 'api-url/beacon';
```

## Debugging

In production you can test things are working by manually running the scheduled task:

\tool_beacon\task\signal_beacon


## Mock beacon

To aid in testing a mock beacon service can be setup:

```
$CFG->forced_plugin_settings['tool_beacon']['secretkey'] = 'testing';
$CFG->forced_plugin_settings['tool_beacon']['beaconbaseurl'] = $CFG->wwwroot . '/admin/tool/beacon/tests/mock.php/';
```
