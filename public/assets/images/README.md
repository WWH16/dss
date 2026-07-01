# Image Assets

This folder contains images used in the ISU Canteen DSS system.

## Required Images

### 1. isu-logo.png
- **Purpose**: ISU Seal/Logo displayed in the navbar
- **Location**: `assets/images/isu-logo.png`
- **Recommended Size**: 40-50px height (width auto-scales)
- **Format**: PNG with transparent background preferred
- **Description**: The circular ISU seal with green border and "1976"

### 2. food-court.jpg
- **Purpose**: Background image for the hero section on the home page
- **Location**: `assets/images/food-court.jpg`
- **Recommended Size**: 1500px width minimum (will be responsive)
- **Format**: JPG or PNG
- **Description**: Photo of "THE UNIVERSITY FOOD COURT" building

## How to Add Images

1. Save the ISU logo as `isu-logo.png` in this directory
2. Save the food court photo as `food-court.jpg` in this directory
3. Refresh your browser to see the updated home page with:
   - Logo in the navbar next to "ISU Canteen DSS"
   - Food court photo as the hero section background

## Current Image References

- `index.php` (line 14): References `assets/images/isu-logo.png`
- `assets/css/style.css` (line 19): References `../images/food-court.jpg` as hero background
