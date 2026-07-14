import { execSync } from 'child_process';
import type { FullConfig } from '@playwright/test';

async function globalSetup(config: FullConfig) {
  if (process.env.SKIP_DB_RESET) {
    console.log('Skipping database migrations and seeds (SKIP_DB_RESET is set).');
    return;
  }
  
  console.log('Running database migrations and seeds...');
  try {
    // Jalankan migrate:fresh --seed di root Laravel
    execSync('php artisan migrate:fresh --seed', { stdio: 'inherit' });
    console.log('Database seeded successfully.');
  } catch (error) {
    console.error('Failed to run database migrations and seeds:', error);
    throw error;
  }
}

export default globalSetup;
