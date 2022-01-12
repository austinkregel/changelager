const defaultTheme = require('tailwindcss/defaultTheme');

module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Nunito', ...defaultTheme.fontFamily.sans],
            },
            typography: (theme) => ({
                default: {
                    css: {
                        color: theme('colors.gray.900'),
    
                        a: {
                            color: theme('colors.blue.500'),
                            '&:hover': {
                                color: theme('colors.blue.700'),
                            },
                        },
                    },
                },
    
                dark: {
                    css: {
                        color: theme('colors.gray.100'),
    
                        a: {
                            color: theme('colors.blue.100'),
                            '&:hover': {
                                color: theme('colors.blue.100'),
                            },
                        },
                    },
                },
            }),
    
    
        },
    },

    plugins: [require('@tailwindcss/forms'), require('@tailwindcss/typography')],
};
