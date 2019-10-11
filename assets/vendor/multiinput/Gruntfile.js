module.exports = function (grunt) {
    // Project configuration.
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        banner: '/*! <%= pkg.name %> * Version: <%= pkg.version %> * Build date: <%= grunt.template.today("yyyy-mm-dd") %> */',
        usebanner: {
            js: {
                options: {
                    position: 'top',
                    banner: '<%= banner %>'
                },
                files: {
                    src: [
                        'dist/js/jq.multiinput.min.js'
                    ]
                }
            },
            css: {
                options: {
                    position: 'bottom',
                    banner: '<%= banner %>'
                },
                files: {
                    src: [
                        'dist/css/jq.multiinput.min.css'
                    ]
                }
            }
        },
        uglify: {
            js: {
                sourceMap: true,
                src: [
                    'src/js/jq.multiinput.js'
                ],
                dest: 'dist/js/jq.multiinput.min.js'
            }
        },
        sass: {
            options: {
                implementation: require('node-sass'),
                sourceMap: false
            },
            css: {
                files: {
                    'dist/css/jq.multiinput.min.css': 'src/scss/jq.multiinput.scss'
                }
            }
        },
        postcss: {
            options: {
                map: false,
                processors: [
                    require('pixrem')(),
                    require('autoprefixer')({
                        browsers: ['last 2 version']
                    }),
                    require('cssnano')({
                        preset: ['default', {
                            discardComments: {
                                removeAll: true
                            },
                            zindex: false
                        }]
                    })
                ]
            },
            css: {
                src: 'dist/css/jq.multiinput.min.css'
            }
        },
        watch: {
            css: {
                files: [
                    'src/scss/*.scss'
                ],
                tasks: ['sass', 'postcss', 'usebanner:css']
            },
            js: {
                files: [
                    'src/js/*.js'
                ],
                tasks: ['uglify', 'usebanner:js']
            }
        }
    });

    //load the packages
    grunt.loadNpmTasks('grunt-banner');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-postcss');
    grunt.loadNpmTasks('grunt-sass');

    //register the task
    grunt.registerTask('build', ['uglify', 'sass', 'postcss', 'usebanner']);
};