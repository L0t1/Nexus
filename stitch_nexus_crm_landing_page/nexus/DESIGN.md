---
name: Nexus
colors:
  surface: '#131315'
  surface-dim: '#131315'
  surface-bright: '#39393b'
  surface-container-lowest: '#0e0e10'
  surface-container-low: '#1c1b1d'
  surface-container: '#201f22'
  surface-container-high: '#2a2a2c'
  surface-container-highest: '#353437'
  on-surface: '#e5e1e4'
  on-surface-variant: '#bcc9cd'
  inverse-surface: '#e5e1e4'
  inverse-on-surface: '#313032'
  outline: '#869397'
  outline-variant: '#3d494c'
  surface-tint: '#4cd7f6'
  primary: '#4cd7f6'
  on-primary: '#003640'
  primary-container: '#06b6d4'
  on-primary-container: '#00424f'
  inverse-primary: '#00687a'
  secondary: '#4edea3'
  on-secondary: '#003824'
  secondary-container: '#00a572'
  on-secondary-container: '#00311f'
  tertiary: '#c6c6c7'
  on-tertiary: '#2f3131'
  tertiary-container: '#a5a6a6'
  on-tertiary-container: '#3a3c3c'
  error: '#ffb4ab'
  on-error: '#690005'
  error-container: '#93000a'
  on-error-container: '#ffdad6'
  primary-fixed: '#acedff'
  primary-fixed-dim: '#4cd7f6'
  on-primary-fixed: '#001f26'
  on-primary-fixed-variant: '#004e5c'
  secondary-fixed: '#6ffbbe'
  secondary-fixed-dim: '#4edea3'
  on-secondary-fixed: '#002113'
  on-secondary-fixed-variant: '#005236'
  tertiary-fixed: '#e2e2e2'
  tertiary-fixed-dim: '#c6c6c7'
  on-tertiary-fixed: '#1a1c1c'
  on-tertiary-fixed-variant: '#454747'
  background: '#131315'
  on-background: '#e5e1e4'
  surface-variant: '#353437'
typography:
  h1:
    fontFamily: Plus Jakarta Sans
    fontSize: 48px
    fontWeight: '700'
    lineHeight: '1.2'
    letterSpacing: -0.02em
  h2:
    fontFamily: Plus Jakarta Sans
    fontSize: 36px
    fontWeight: '700'
    lineHeight: '1.2'
    letterSpacing: -0.02em
  h3:
    fontFamily: Plus Jakarta Sans
    fontSize: 24px
    fontWeight: '600'
    lineHeight: '1.3'
    letterSpacing: -0.01em
  body-lg:
    fontFamily: Plus Jakarta Sans
    fontSize: 18px
    fontWeight: '400'
    lineHeight: '1.6'
    letterSpacing: '0'
  body-md:
    fontFamily: Plus Jakarta Sans
    fontSize: 16px
    fontWeight: '400'
    lineHeight: '1.6'
    letterSpacing: '0'
  label-sm:
    fontFamily: Plus Jakarta Sans
    fontSize: 13px
    fontWeight: '600'
    lineHeight: '1'
    letterSpacing: 0.05em
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  unit: 4px
  xs: 4px
  sm: 8px
  md: 16px
  lg: 24px
  xl: 40px
  2xl: 64px
  gutter: 24px
  margin: 32px
---

## Brand & Style
The design system is engineered to evoke a sense of sophisticated technical precision and elite craftsmanship. It targets high-growth enterprises that require a centralized, "command center" feel for their customer relations. 

The aesthetic is rooted in **Glassmorphism**, utilizing multi-layered translucency to suggest depth without clutter. The interface feels "expensive" through the restraint of its effects: light is used as a material, with subtle glows and refractive borders that guide the eye toward high-conversion actions. It is a futuristic, tech-forward environment that balances high-utility CRM data with a premium, cinematic user experience.

## Colors
The palette is anchored in a deep, obsidian base to provide maximum contrast for the glass elements. 

- **Base Layer:** A rich, dark neutral (#09090b) serves as the foundation.
- **Atmospheric Accents:** Deep mesh gradients in Cyan and Emerald are applied to the background at low opacities (10-15%) to provide a sense of vastness.
- **Glass Surfaces:** Semi-transparent whites create the "frosted" layers, while borders use a subtle white glow to define edges against the dark background.
- **Action Gradients:** Primary interactions utilize a vibrant linear gradient from Cyan (#06b6d4) to Emerald (#10b981), signifying energy and growth.

## Typography
This design system employs **Plus Jakarta Sans** to maintain a modern, clean, yet slightly approachable geometric feel. 

Headings are rendered in high-contrast white (#ffffff) with tight letter-spacing to create a bold, "tech-heavy" editorial look. Body text transitions to a muted Slate (#94a3b8) to ensure long-term readability and to reinforce the visual hierarchy where data and labels are secondary to primary navigation and KPIs. Small labels and data points use increased letter-spacing and semi-bold weights to maintain legibility against blurred backgrounds.

## Layout & Spacing
The layout follows a **Fixed Grid** philosophy for dashboard views and a **Fluid Grid** for communication feeds. 

- **The Grid:** A 12-column system with 24px gutters provides the structural skeleton. 
- **Rhythm:** An 8pt linear scaling system (with a 4pt half-step for tight UI) ensures consistent vertical rhythm.
- **Negative Space:** Large margins (32px+) are encouraged between major glass containers to allow the background mesh gradients to breathe, enhancing the "floating" effect of the UI components.

## Elevation & Depth
Depth is communicated through **Glassmorphism** and light refraction rather than traditional drop shadows.

- **Layer 0 (Background):** The #09090b base with mesh glow.
- **Layer 1 (Cards/Panels):** Semi-transparent white (3% opacity) with a `backdrop-blur-xl` (20px-24px). Borders are 1px solid `rgba(255, 255, 255, 0.1)`.
- **Layer 2 (Popovers/Modals):** Slightly higher opacity (6%) and a more pronounced border glow. A very soft, large-radius ambient shadow with a Cyan tint (#06b6d4 at 10% opacity) is used to lift these elements off the surface.
- **Refractive Edges:** All containers feature a top-left inner highlight to simulate a light source reflecting off a glass edge.

## Shapes
The shape language is consistently **Rounded**, striking a balance between organic friendliness and professional structure. 

Standard containers and cards utilize a 1rem (16px) corner radius. Smaller interactive elements like input fields and tags use a 0.5rem (8px) radius. This differentiation helps users subconsciously categorize "structural" elements versus "actionable" elements. Buttons may occasionally use a pill-shape for high-emphasis CTAs to break the grid's rectilinearity.

## Components
- **Buttons:** Primary buttons are vibrant Cyan-to-Emerald gradients with white text. Secondary buttons use the glass style (blur + border) with a hover state that increases border brightness.
- **Input Fields:** Dark, recessed backgrounds (rgba(0,0,0,0.2)) with a 1px border that glows Cyan when focused.
- **Chips/Badges:** Small, high-radius elements with low-opacity fills of the accent colors (e.g., 10% Cyan fill with 100% Cyan text).
- **Cards:** The core container of the system. Must include a 1px `white/10` border and `backdrop-blur-xl`.
- **Lists:** Clean rows separated by subtle `white/05` dividers. Hover states should trigger a light "wash" of `white/05` over the row.
- **Omni-channel Feed:** Distinctive message bubbles using varying levels of glass transparency to distinguish between internal notes (more opaque) and customer messages (more transparent).