import os
import re

directory = 'resources/views'

replacements = [
    (r'\btext-slate-200\b', 'text-slate-800 dark:text-slate-200'),
    (r'\btext-slate-300\b', 'text-slate-700 dark:text-slate-300'),
    (r'\btext-slate-400\b', 'text-slate-500 dark:text-slate-400'),
    (r'\btext-slate-500\b', 'text-slate-400 dark:text-slate-500'),
    (r'\btext-slate-600\b', 'text-slate-400 dark:text-slate-600'),
    
    (r'\bbg-indigo-500/5\b', 'bg-indigo-50 dark:bg-indigo-500/5'),
    (r'\bbg-indigo-500/10\b', 'bg-indigo-100 dark:bg-indigo-500/10'),
    (r'\bbg-indigo-500/20\b', 'bg-indigo-100 dark:bg-indigo-500/20'),
    (r'\btext-indigo-300\b', 'text-indigo-600 dark:text-indigo-300'),
    (r'\btext-indigo-400\b', 'text-indigo-600 dark:text-indigo-400'),
    
    (r'\bborder-white/5\b', 'border-slate-200 dark:border-white/5'),
    (r'\bborder-white/\[0\.04\]\b', 'border-slate-200 dark:border-white/[0.04]'),
    (r'\bbg-white/\[0\.04\]\b', 'bg-slate-100 dark:bg-white/[0.04]'),
    (r'\bbg-white/\[0\.03\]\b', 'bg-slate-50 dark:bg-white/[0.03]'),
    
    (r'\bbg-blue-500/10\b', 'bg-blue-100 dark:bg-blue-500/10'),
    (r'\btext-blue-400\b', 'text-blue-600 dark:text-blue-400'),
    
    (r'\bbg-emerald-500/10\b', 'bg-emerald-100 dark:bg-emerald-500/10'),
    (r'\btext-emerald-300\b', 'text-emerald-600 dark:text-emerald-300'),
    (r'\btext-emerald-400\b', 'text-emerald-600 dark:text-emerald-400'),
    
    (r'\bbg-red-500/5\b', 'bg-red-50 dark:bg-red-500/5'),
    (r'\bbg-red-500/10\b', 'bg-red-100 dark:bg-red-500/10'),
    (r'\btext-red-300\b', 'text-red-600 dark:text-red-300'),
    (r'\btext-red-400\b', 'text-red-600 dark:text-red-400'),
    (r'\border-red-500/20\b', 'border-red-200 dark:border-red-500/20'),
    
    (r'\btext-amber-400\b', 'text-amber-600 dark:text-amber-400'),
    (r'\btext-orange-300\b', 'text-orange-600 dark:text-orange-300'),
]

for root, _, files in os.walk(directory):
    for file in files:
        if file.endswith('.blade.php'):
            filepath = os.path.join(root, file)
            with open(filepath, 'r') as f:
                content = f.read()
            
            original_content = content
            for search, replace in replacements:
                # To prevent double replacing (e.g. if dark:text-slate-200 is already there)
                # we only replace if it's not immediately preceded by dark:
                content = re.sub(r'(?<!dark:)' + search, replace, content)
                
            if content != original_content:
                with open(filepath, 'w') as f:
                    f.write(content)
                print(f"Updated {filepath}")
