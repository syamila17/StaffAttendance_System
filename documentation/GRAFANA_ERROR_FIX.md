# Grafana Error Fix - ERR_EMPTY_RESPONSE

## Problem
Grafana container was failing with error: "localhost didn't send any data. ERR_EMPTY_RESPONSE"

## Root Cause
The docker-compose.yml was trying to install plugins that don't exist or are incompatible:

1. **grafana-mysql-datasource** - Returns 404 error "Plugin not found"
2. **grafana-simple-json-datasource** - Uses deprecated Angular framework which is no longer supported in Grafana 12.3.0

These plugin installation failures caused the entire Grafana service to fail startup.

## Solution Applied
Updated the `docker-compose.yml` file to remove the problematic plugins:

**Before:**
```yaml
- GF_INSTALL_PLUGINS=grafana-mysql-datasource,grafana-clock-panel,grafana-simple-json-datasource
```

**After:**
```yaml
- GF_INSTALL_PLUGINS=grafana-clock-panel
```

Kept only `grafana-clock-panel` which is a valid, built-in plugin.

## Alternative: Using Built-in MySQL Datasource
Grafana 12.3.0 includes built-in MySQL datasource support. You can:
1. Access Grafana dashboard (http://localhost:3000)
2. Go to Connections → Data Sources
3. Add a new MySQL datasource with:
   - Host: `mysql`
   - Database: `staffAttend_data`
   - Username: `root`
   - Password: `root`

## Next Steps
1. Remove the Grafana volume to clear old configuration
2. Restart the Grafana container
3. Access Grafana at http://localhost:3000 with credentials (admin/admin)
4. Configure MySQL datasource manually if needed

## Files Modified
- `docker-compose.yml` - Line with GF_INSTALL_PLUGINS environment variable
