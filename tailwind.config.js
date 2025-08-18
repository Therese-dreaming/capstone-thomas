export default {
	safelist: [
		{
			pattern: /(bg|text|border|ring|fill|stroke)-(red|green|blue|gray|orange)-(600|700|800)/,
			variants: ['hover', 'focus', 'active']
		},
		// Gradient utilities
		'bg-gradient-to-r', 'from-maroon', 'to-red-700',
		// Maroon color utilities
		'bg-maroon', 'text-maroon', 'border-maroon', 'hover:bg-maroon',
		// Explicit singletons often used
		'bg-red-700', 'hover:bg-red-700', 'bg-red-800', 'hover:bg-red-800',
		'focus:ring-red-700', 'focus:ring-red-800', 'text-red-700', 'text-red-800',
		'bg-orange-600', 'hover:bg-orange-600', 'bg-orange-700', 'hover:bg-orange-700',
		'focus:ring-orange-600', 'focus:ring-orange-700', 'text-orange-600', 'text-orange-700'
	]
}; 