import os

file_path = 'resources/views/layouts/app.blade.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# Replace active classes
content = content.replace(\"'bg-[#0091DA]/10 text-[#0091DA]'\", \"'sidebar-link-active'\")

# Replace style tag
old_style = \"<style>body { font-family: 'Hanken Grotesk', sans-serif; }</style>\"
new_style = \"\"\"<style>
        body { font-family: 'Hanken Grotesk', sans-serif; }
        
        /* Smooth Page Transition */
        @keyframes pageFadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .page-transition {
            animation: pageFadeIn 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        /* Active Sidebar Indicator */
        .sidebar-link-active {
            background-color: rgb(0 145 218 / 0.1);
            color: #0091DA;
            position: relative;
            transition: all 0.3s ease;
        }
        .sidebar-link-active::before {
            content: '';
            position: absolute;
            left: -12px;
            top: 0;
            bottom: 0;
            width: 4px;
            background-color: #0091DA;
            border-top-right-radius: 4px;
            border-bottom-right-radius: 4px;
            box-shadow: 1px 0 6px rgba(0, 145, 218, 0.4);
        }
    </style>\"\"\"
content = content.replace(old_style, new_style)

# Add page transition class
old_div = '<div class=\"max-w-5xl mx-auto\">\\n                @yield(\\'content\\')'
new_div = '<div class=\"max-w-5xl mx-auto page-transition\">\\n                @yield(\\'content\\')'
content = content.replace(old_div, new_div)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)

print(\"Replaced successfully\")
