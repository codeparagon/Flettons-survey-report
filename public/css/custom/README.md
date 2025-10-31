# Custom CSS Structure

This directory contains all the custom CSS files for the SurvAI application, organized by category for easy maintenance and updates.

## File Structure

```
public/css/custom/
├── main.css          # Main file that imports all other CSS files
├── general.css       # General layout and global styles
├── datatable.css     # DataTable specific styles
├── buttons.css       # Button styles
├── badges.css        # Badge styles
├── cards.css         # Card component styles
├── tables.css        # Table styles
├── forms.css         # Form input and control styles
└── README.md         # This file
```

## Usage

The main CSS file (`main.css`) automatically imports all other CSS files. It's included in the main layout file (`resources/views/layouts/app.blade.php`):

```blade
<link rel="stylesheet" href="{{ asset('css/custom/main.css') }}">
```

## File Descriptions

### main.css
- Main entry point for all custom styles
- Imports all other CSS files using `@import`
- Keep this file organized by import order

### general.css
Contains general layout and global styles including:
- Dashboard layout structure
- Sidebar styling
- Body and heading styles
- Alert styling
- Footer styling

### datatable.css
Contains all DataTable specific styles including:
- Table header and body styling
- Pagination controls
- Filter and search inputs
- Responsive mobile styles
- Loading states

### buttons.css
Contains all button styles including:
- Primary, secondary, success, danger, warning, info buttons
- Outline button variants
- Hover states and transitions
- Small button sizes

### badges.css
Contains badge component styles:
- Success, warning, danger, info, secondary badges
- Consistent padding and border-radius

### cards.css
Contains card component styles:
- Card backgrounds and borders
- Card header and body styling

### tables.css
Contains general table styles (non-DataTable):
- Table backgrounds and colors
- Header and body styling
- Hover effects

### forms.css
Contains form input and control styles:
- Input fields
- Focus states
- Label styling

## Color Palette

The application uses the following color scheme:

- **Primary Dark**: `#1a202c`
- **Primary Light**: `#c1ec4a`
- **Background**: `#f8fafc`
- **White**: `#ffffff`
- **Gray Borders**: `#e5e7eb`
- **Text**: `#1a202c`
- **Hover Dark**: `#2d3748`
- **Hover Light**: `#b0d93f`

## Best Practices

1. **Categorize by Component**: Keep styles organized by component or purpose
2. **Use !important Sparingly**: Only when necessary to override third-party styles
3. **Responsive Design**: Include mobile-first responsive styles where applicable
4. **Consistent Naming**: Use BEM or similar naming conventions
5. **Documentation**: Add comments for complex styles or hacks

## Editing Guidelines

When editing these files:

1. Edit the specific category file (e.g., `buttons.css` for button changes)
2. Avoid editing `main.css` except to add/remove imports
3. Test changes across different screen sizes
4. Maintain consistent color usage across files
5. Use the same unit system (prefer `rem` for typography, `px` for borders)

## Browser Support

These styles are designed to work with:
- Modern browsers (Chrome, Firefox, Safari, Edge)
- Internet Explorer 11+ (with some degraded features)
- Mobile browsers (iOS Safari, Chrome Mobile)

## Maintenance

- Keep files focused on their specific purpose
- Remove unused styles periodically
- Review and consolidate similar styles
- Update this README when adding new categories or files
