module.exports = function(grunt) { //The wrapper function

    // Project configuration & task configuration
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        makepot: {
            target: {
                options: {
                    domainPath: 'languages/',
                    potFilename: 'wow_armory_character.pot',
                    potHeaders: {
                        'report-msgid-bugs-to': 'https://github.com/cooperaj/wow-armory-character/issues',
                    },
                    type: 'wp-plugin',
                }
            }
        }
    });

    //Loading the plug-ins
    grunt.loadNpmTasks('grunt-wp-i18n');

    // Default task(s), executed when you run 'grunt'
    grunt.registerTask('default', ['makepot']);

};
