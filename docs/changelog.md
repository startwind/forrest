# Changelog

Forrest is constantly evolving. Sometimes so fast that you might not even get around to trying out the new features. For this reason, we have introduced a changelog.

- **1.0.6** - We store the recent parameters of a function call and suggest the same value the next time a command is called.


- **1.0.5** - In addition to the composer files we can now also read `package.json` files from npm. [read more](friends.md)


- **1.0.4** - If a composer file is found in the current directory, the attached scripts will also be shown via Forrest and can run the same way as Forrest commands. [read more](friends.md)


- **1.0.3** - We introduced piggyback repositories. Every project now can have its own `forrest.yml` file. When Forrest finds that file it automatically shows the commands. This way nothing has to be installed. [read more](creating-repository.md#piggyback-repository)


- **1.0.2** - Commands can now be multi-line commands. Each command will run after the other.


- **1.0.1** - We introduced enums as values for parameters. The creator of a command can set a predefined list of values for a parameter the user can chose from. [read more](formats/yaml-format.md#the-yaml-repository-format)


- **1.0.0** - First stable version with all must haves we wanted to have in Forrest.


- **0.9.1** - `file` can be used as a shortcut for `commands:file`.


- **0.9.0** - You can search the registered commands for pattern. This is done via `search:pattern`


- **0.8.0** - We introduced the inverse search on files. It is now possible to run `search:file` with a given file name and Forrest will return all commands that are applicable for this file. [read more](commands/search_file.md)
