# Komodo Deployment Guide

This guide outlines the steps to deploy **PKN Portal** using [Komodo PaaS](https://komo.do/).

## 1. Prerequisites

- A server with **Komodo Periphery** installed and connected to your **Komodo Core**.
- Access to the Git repository containing this project.
- A Docker Registry account (e.g., Docker Hub, GHCR) if you want to push/pull images (optional if building and deploying on the same server).

## 2. Step 1: Build Configuration

In the Komodo UI, create a new **Build**:

1. **Repository**: Link your Git repository (e.g., `username/pkn-portal`).
2. **Branch**: Select the production branch (usually `main`).
3. **Build Path**: Set to `.` (the root of the repository).
4. **Dockerfile Path**: Set to `docker/production/Dockerfile`.
5. **Image Name**: e.g., `pkn-portal-app`.
6. **Registry**: Select your configured Docker registry.

## 3. Step 2: Stack Configuration

Create a new **Stack** in Komodo:

1. **Compose File Source**:
    - Link `docker-compose.prod.yml` directly from your Git repository (if using "Git" source).
    - Alternatively, copy the contents of `docker-compose.prod.yml` into the Komodo Compose editor.
2. **Environment Variables**:
    - Pass your environment variables (from `.env.production.example`) using Komodo's **Variable** tab or an uploaded `.env` file in the Stack directory.
    - **Important**: Ensure `APP_KEY` is set to a secure 32-character string.
    - **Important**: Set `RUN_MIGRATIONS=true` if you want the first deployment to run database migrations automatically.

## 4. Step 3: Deployment

1. **Build the Image**: Run the build manually or set up a webhook to trigger on Git push.
2. **Deploy the Stack**:
    - Once the image is built, go to your Stack and click **Deploy**.
    - Komodo will pull the latest image and start the containers defined in the compose file.

## 5. Post-Deployment Verification

- Check the **Containers** tab in Komodo to ensure all services (`app`, `nginx`, `pgsql`, `redis`, `worker`, `scheduler`) are **Healthy**.
- Visit your `APP_URL` to verify the application is live.
- Check the logs of the `worker` container to ensure background jobs are processing correctly.

## 6. Troubleshooting

- **Migrations failed**: Check the `app` container logs. Ensure `DB_HOST` is set to `pgsql` and credentials are correct.
- **Vite assets missing**: The `Dockerfile` handles the production build, so ensure the `public/build` directory is correctly generated (done automatically in the multi-stage build).
- **Healthchecks failing**: Ensure the containers have access to each other over the internal Docker network (automatic with Compose).
