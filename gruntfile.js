module.exports = function(grunt) {

    grunt.initConfig({

        pkg: grunt.file.readJSON('package.json'),

        // JAVASCRIPT

        jshint: {
            // files: ['assets/js/*.js'],
            options: {
                ignores: ['assets/js/*.min.js']
            },
            ignore_warning: {
                options: {
                    '-W099': true
                },
                src: ['assets/js/*.js']
            }

        },

        uglify: {
            front: {
                src: 'assets/js/simple-events.js',
                dest: 'assets/js/simple-events.min.js'
            }
        },

        // CSS

        autoprefixer: {
            front: {
                src: 'assets/css/simple-events.css',
                dest: 'assets/css/simple-events.css'
            },
            admin: {
                src: 'assets/css/simple-events-admin.css',
                dest: 'assets/css/simple-events-admin.css'
            }
        },

        compass: {
            dev: {
                options: {
                    app: 'stand_alone',
                    sassDir: 'assets/css',
                    cssDir: 'assets/css',
                    outputStyle: 'nested',
                    environment: 'development'
                }
            }
        },

        csslint: {
            build: {
                options: {
                    import: false
                },
                src: ['assets/css/*.css']
            }
        },

        cssmin: {
            minify: {
                files: {
                    'assets/css/simple-events.css' : 'assets/css/simple-events.css',
                    'assets/css/simple-events-admin.css' : 'assets/css/simple-events-admin.css'
                }
            }
        },

        // IMAGES

        imagemin: {
            dynamic: {
                files: [{
                    expand: true,
                    cwd: 'assets/img/',
                    src: ['*.{png,jpg,gif}'],
                    dest: 'assets/img/'
                }]
            }
        },

        // WATCH
        
        watch: {
            scripts: {
                files: ['assets/js/*.js'],
                tasks: ['jshint', 'uglify'],
                options: {
                    spawn: false,
                },
            },
            css: {
                files: ['assets/css/*.scss'],
                tasks: ['compass', 'autoprefixer', 'cssmin'],
                options: {
                    livereload: true,
                    spawn: false,
                }
            },
            images: {
                files: ['assets/img/*.{png,jpg,gif}'],
                tasks: ['imagemin'],
                options: {
                    spawn: false,
                }
            }
        }

    });

    // 3. Where we tell Grunt we plan to use this plug-in.
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-imagemin');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-autoprefixer');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-compass');
    grunt.loadNpmTasks('grunt-contrib-jshint');
    grunt.loadNpmTasks('grunt-contrib-csslint');

    // 4. Where we tell Grunt what to do when we type "grunt" into the terminal.
    grunt.registerTask('default', ['watch', 'concat', 'uglify']);

};