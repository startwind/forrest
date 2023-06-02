# Changelog

Forrest is constantly evolving. Sometimes so fast that you might not even get around to trying out the new features. For this reason, we have introduced a changelog.

- **develop**
  - Fixed `--force` bug. 


- **2.1.0**
  - Fixed bug where search:tool does not find any commands but still asks "Which tool to run".
  - Rebuild the "home screen" of Forrest. It will now show all the important Forrest commands at one glance.
  - New function for enums `docker-images`. This will show all docker images that are currently running. [read more](commands/enrichment/functions.md#docker-images)
  - Enrich tool search: We are now able to enrich the tool search with fundamental information about the tool and also can link websites to help the user to learn more about the tool. 


- **2.0.0**
  - **Search via API**. With this new major release we are able to search for commands using an API. This way we can find fitting commands much faster and are able to extend the systems on the fly. 
  - **Debug mode** We introduced a debug mode that will be activated via `--debug` and will show more verbose information if an error occurs.


- **1.5.0**
  - Shortcut for `search:file`. The functionality can now be used via the `run` command by simply using the file as first argument.
  - New function for docker commands. It is now possible to use functions in enums. In this particular case you can use `${docker-names()}` and it will show all the running docker containers in the selection. [read more](commands/enrichment/functions.md#docker-names)
  - If a command is not runnable it will be copied to the clipboard so it is easy to paste it to the command line very easy. [read more](formats/yaml-format.md#parameter)
  - The `repository:list` command now shows if a repository is writeable.
  - New constraint `identifier` checks for values that only contain lower-case lettern and numbers. [read more](formats/yaml-format.md#constraints--optional-)


- **1.4.0**
  - 5 minute cache: Forrest now stores all results from the repositories for 5 minutes. This speeds up the `run` and `list` command.
  - Advanced enums: enums can now be key-value pairs. This helps when the actual value of the prompt is a long and unhandy string. [read more](formats/yaml-format.md#enum--optional-) 
  - Filter `search:file` command by adding a pattern as second argument. 
  - `add` is now a shortcut for `repository:command:add`.
  - Constraints can now be used to pre-validate parameter values. [read more](formats/yaml-format.md#constraints--optional-)


- **1.3.0**
  - Introducing external and private directories. It is now possible to register an external directory with a list of repositories to Forrest. This comes handy if a team has a lot of different repositories and wants a clean structure. It can also be used to create private directories. [read more](directories/directories.mds)
  - A command can define an `output-format`. This is basically a `sprintf` command for the commands output. [read more](formats/yaml-format.md#output-format)
  - Password handling. Parameters can now be defined as password and will nether be shown when entering the data nor stored in history. [read more](formats/yaml-format.md#type--optional-)
  - A command can now be flagged so it will not be shown in the Forrest commands history. [read more](formats/yaml-format.md#parameter) 


- **1.2.0** 
  - Repositories can now be on private GitHub repositories. This way it is easier to store internal/secret commands.  
  - fixed a bug with directory name that includes the word `function`.
  - Added `pattern` as shortcut for `search:pattern` and `tool` for `search:tool`. 
  - Fixed sort order when listing commands
  - `search:pattern` is case in-sensitive now


- **1.1.0**
  - Introducing functions. It is now possible to use dynamic content within a prompt like the current date in all format variation. [read more](commands/enrichment/functions.md)
  - `commands:list` has now the repository as optional argument. This way you can filter a long commands list to see only the needed ones.
  - Immediately run commands from the search command. This removed one step from the workflow.
  - [Functions](commands/enrichment/functions.md) can now also be used for the default value. 
  - We added `env()` as a new function. [read more](commands/enrichment/functions.md)
  - We store the recent parameters of a function call and suggest the same value the next time a command is called.
  - Prefill the commands parameters via command line to be able to run the command without "questions". [read more](commands/commands_run.md)
  - Bugfix: Empty repositories are not shown in the list.
  - In addition to the composer files we can now also read `package.json` files from npm. [read more](friends.md)
   - If a composer file is found in the current directory, the attached scripts will also be shown via Forrest and can run the same way as Forrest commands. [read more](friends.md)
   - We introduced piggyback repositories. Every project now can have its own `forrest.yml` file. When Forrest finds that file it automatically shows the commands. This way nothing has to be installed. [read more](creating-repository.md#piggyback-repository)
   - Commands can now be multi-line commands. Each command will run after the other.
   - We introduced enums as values for parameters. The creator of a command can set a predefined list of values for a parameter the user can chose from. [read more](formats/yaml-format.md#the-yaml-repository-format)


- **1.0.0** 
  - **First stable version with all must haves we wanted to have in Forrest.**
  - `file` can be used as a shortcut for `commands:file`.
  - You can search the registered commands for pattern. This is done via `search:pattern`
  - We introduced the inverse search on files. It is now possible to run `search:file` with a given file name and Forrest will return all commands that are applicable for this file. [read more](commands/search_file.md)
