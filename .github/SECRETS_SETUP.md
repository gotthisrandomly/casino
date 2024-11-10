# Setting Up GitHub Secrets

Follow these steps to set up the required secrets for the CI/CD pipeline:

1. Generate SSH Key Pair:
```bash
ssh-keygen -t ed25519 -C "casino-deploy"
# Save it as casino-deploy or your preferred name
```

2. Add the public key to your Termux server:
```bash
# On your Termux device
mkdir -p ~/.ssh
# Copy the content of casino-deploy.pub to:
echo "YOUR_PUBLIC_KEY" >> ~/.ssh/authorized_keys
chmod 600 ~/.ssh/authorized_keys
chmod 700 ~/.ssh
```

3. Go to your GitHub repository settings:
   - Navigate to Settings > Secrets and variables > Actions
   - Click "New repository secret"

4. Add the following secrets:

## SSH_PRIVATE_KEY
- Name: `SSH_PRIVATE_KEY`
- Value: The entire content of your private key file (casino-deploy)
```bash
cat casino-deploy
# Copy the entire output including the BEGIN and END lines
```

## DEPLOY_HOST
- Name: `DEPLOY_HOST`
- Value: Your Termux device's IP address or domain
```bash
# On Termux, get your IP:
ip addr show | grep 'inet '
```

## DEPLOY_USER
- Name: `DEPLOY_USER`
- Value: Your Termux username (usually `u0_a###`)
```bash
# On Termux, get your username:
whoami
```

## JWT_SECRET
- Name: `JWT_SECRET`
- Value: Generate a secure random string:
```bash
openssl rand -base64 32
```

## ADMIN_EMAIL_NOTIFICATIONS
- Name: `ADMIN_EMAIL_NOTIFICATIONS`
- Value: The email where you want to receive admin notifications

## SLACK_WEBHOOK (Optional)
- Name: `SLACK_WEBHOOK`
- Value: Your Slack webhook URL for deployment notifications
- Get it from: https://slack.com/apps/A0F7XDUAZ-incoming-webhooks

## SENTRY_DSN (Optional)
- Name: `SENTRY_DSN`
- Value: Your Sentry DSN for error tracking
- Get it from: https://sentry.io/

## MAXMIND_LICENSE_KEY (Optional)
- Name: `MAXMIND_LICENSE_KEY`
- Value: Your MaxMind license key for GeoIP services
- Get it from: https://www.maxmind.com/

5. Verify Secrets:
- Go to the "Actions" tab in your repository
- Check if the CI/CD workflow runs successfully