import os
import re

file_path = 'resources/views/layouts/app.blade.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# CSS to inject
css_code = \"\"\"
        /* Magic Indicator Navigation */
        .magic-cutout {
            position: absolute;
            top: -24px;
            width: 64px;
            height: 64px;
            background-color: #f9fafb; /* App bg color */
            border-radius: 50%;
        }
        .magic-cutout::before, .magic-cutout::after {
            content: '';
            position: absolute;
            top: 24px;
            width: 24px;
            height: 24px;
            background-color: transparent;
        }
        .magic-cutout::before {
            left: -25px;
            border-top-right-radius: 24px;
            box-shadow: 0 -12px 0 0 #f9fafb;
        }
        .magic-cutout::after {
            right: -25px;
            border-top-left-radius: 24px;
            box-shadow: 0 -12px 0 0 #f9fafb;
        }
        .magic-circle {
            position: absolute;
            top: 6px;
            left: 6px;
            width: 52px;
            height: 52px;
            background-color: #0091DA;
            border-radius: 50%;
            z-index: 11;
        }

        .nav-item .icon-wrapper {
            transform: translateY(4px);
            transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            color: #9ca3af;
        }
        .nav-item .text-label {
            opacity: 0;
            transform: translateY(12px);
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .nav-item.active .icon-wrapper {
            transform: translateY(-34px);
            color: #ffffff;
            z-index: 20;
        }
        .nav-item.active .text-label {
            opacity: 1;
            transform: translateY(-2px);
        }
\"\"\"

if '/* Magic Indicator Navigation */' not in content:
    content = content.replace('</style>', css_code + '\\n    </style>')

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
print("CSS Injected")
