module.exports = {
  apps: [
    {
      name: 'tokofaarotii',
      script: 'php',
      args: 'artisan octane:start --server=frankenphp --host=0.0.0.0 --port=8080',
      instances: 1,
      autorestart: true,
      watch: false,
      max_memory_restart: '1G',
      env: {
        APP_ENV: 'production',
      }
    },
  ],
};
