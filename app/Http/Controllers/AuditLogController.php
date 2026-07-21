<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $logPath = storage_path('logs');
        
        // Find all audit log files
        $files = File::glob($logPath . '/audit*.log');
        $availableDates = [];
        
        foreach ($files as $file) {
            $filename = basename($file);
            // Extract date from audit-YYYY-MM-DD.log or audit.log
            if (preg_match('/audit-(.*)\.log/', $filename, $matches)) {
                $availableDates[] = $matches[1];
            } elseif ($filename === 'audit.log') {
                $availableDates[] = date('Y-m-d');
            }
        }
        
        $availableDates = array_unique($availableDates);
        rsort($availableDates); // Most recent first
        
        // Default to today or the most recent date if today doesn't exist
        $selectedDate = $request->input('date');
        
        if (!$selectedDate && !empty($availableDates)) {
            $selectedDate = $availableDates[0];
        }

        // Determine which file to read
        $fileToRead = "audit-{$selectedDate}.log";
        $filePath = $logPath . '/' . $fileToRead;
        
        // Fallback for just audit.log if specific date doesn't exist but it's today
        if (!File::exists($filePath) && $selectedDate === date('Y-m-d') && File::exists($logPath . '/audit.log')) {
            $filePath = $logPath . '/audit.log';
        }

        $logs = [];
        if (File::exists($filePath)) {
            $content = File::get($filePath);
            
            // Regex to parse Laravel log format: [Date] Env.Level: Message {Context}
            $pattern = '/^\[(?P<date>[^\]]+)\] (?P<env>\w+)\.(?P<level>\w+): (?P<message>.*?)(?: (?P<context>\{.*\}))?(?: \[\])?\r?$/m';
            
            preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);
            
            foreach ($matches as $match) {
                $contextArray = [];
                if (!empty($match['context'])) {
                    $decoded = json_decode($match['context'], true);
                    $contextArray = is_array($decoded) ? $decoded : ['data' => $match['context']];
                }
                
                $logs[] = [
                    'timestamp' => $match['date'],
                    'level' => $match['level'],
                    'message' => $match['message'],
                    'context' => $contextArray
                ];
            }
            
            // Reverse so newest is at the top
            $logs = array_reverse($logs);
        }

        return view('admin.audit-log.index', compact('logs', 'availableDates', 'selectedDate'));
    }
}
