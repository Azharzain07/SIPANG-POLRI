import re

with open('admin.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Find <body> tag
body_start = content.find('<body>')
if body_start == -1:
    print("Error: <body> tag not found")
    exit(1)

# Find first actual HTML element after <body> (like <div>)
html_pattern = re.search(r'<body>\s*<div class="header">', content[body_start:])
if html_pattern:
    # Replace everything between <body> and <div class="header"> with just <body>
    between_start = body_start + 6  # length of '<body>'
    between_end = body_start + html_pattern.start() + 6
    
    css_section = content[between_start:between_end]
    
    # Only remove if it contains CSS
    if re.search(r'^\s*\.\w+|@keyframes|^\s*}', css_section, re.MULTILINE):
        new_content = content[:between_start] + '\n\n<div class="header">' + content[between_end + 19:]
        
        with open('admin.php', 'w', encoding='utf-8') as f:
            f.write(new_content)
        
        print("CSS removed successfully")
    else:
        print("No CSS found in body section")
else:
    print("Error: Could not find <div class=\"header\">")
