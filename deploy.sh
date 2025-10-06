#!/bin/bash
echo "🚀 Deploy Vue Frontend"

git pull origin main
npm install
npm run build

echo "✅ Deployment selesai"