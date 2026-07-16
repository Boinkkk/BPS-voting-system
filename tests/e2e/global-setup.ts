import { execSync } from 'child_process';
import type { FullConfig } from '@playwright/test';

async function globalSetup(config: FullConfig) {
  // Ambil argumen CLI yang dijalankan user (contoh: npx playwright test e2e/fill-surveys.spec.ts)
  const args = process.argv.slice(2);
  // Cek apakah user sedang menjalankan spesifik file test (selain flag seperti --project=chromium)
  const testFiles = args.filter(arg => arg.endsWith('.spec.ts') || arg.endsWith('.ts'));
  
  // Jika user menjalankan file spesifik, dan file tersebut BUKAN full-cycle.spec.ts, maka SKIP
  const isTargetingOtherTests = testFiles.length > 0 && !testFiles.some(file => file.includes('full-cycle.spec.ts'));

  if (isTargetingOtherTests || process.env.SKIP_DB_RESET) {
    console.log('Skipping database migrations and seeds (hanya jalan default untuk full-cycle atau seluruh suite).');
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
