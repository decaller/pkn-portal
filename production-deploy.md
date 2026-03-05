# Production Deployment Guide (Portainer + GHCR)

This project publishes production images to GitHub Container Registry (GHCR) using `.github/workflows/docker-publish.yml`.

## 1. Prerequisites

- A Docker host managed by Portainer.
- Repository admin/maintainer access in GitHub.
- Your production `.env` values ready.

## 2. Publish Image to GHCR

1. Ensure workflow file exists: `.github/workflows/docker-publish.yml`.
2. Push to `main`.
3. Confirm workflow **Docker Image CI/CD** succeeds in GitHub Actions.
4. Confirm package exists at:
   - `ghcr.io/<github-username-or-org>/<repo>:latest`

## 3. Set GHCR Package Public

Do this once after first successful push:

1. Open GitHub repository.
2. Go to **Packages** and open your container package.
3. Open **Package settings**.
4. Under **Danger Zone** / **Visibility**, set package to **Public** and confirm.

If package is Public, Portainer does not need GHCR credentials to pull.

## 4. Configure Portainer Stack

1. Go to **Stacks** > **Add stack**.
2. Use repository/compose method and point to `docker-compose.prod.yml`.
3. Add environment values from `.env.production.example`.
4. Set:
   - `IMAGE_NAME=ghcr.io/<github-username-or-org>/<repo>`
   - `APP_KEY=<generated Laravel key>`
   - `RUN_MIGRATIONS=true` for first deployment only.

## 5. Deploy and Verify

1. Deploy stack.
2. Wait until all services are healthy (`app`, `nginx`, `worker`, `scheduler`, `pgsql`, `redis`).
3. Open container logs and verify:
   - app boots normally,
   - migrations succeed,
   - worker processes jobs.

## 6. Updating Later

1. Merge changes to `main`.
2. Wait for a new GHCR image (`latest`) to be published by Actions.
3. In Portainer stack update flow, enable **Pull latest image**.
4. Redeploy stack.
