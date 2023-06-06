# Forrest CLI Commands 

## Commands

- `commands:run` - Run a single command.  [More information](commands_run.md)
- `commands:list` - List all registered commands that can be run. [More information](commands_list.md)
- `commands:explain` - Show a single command with all its details and steps that will be executed. It also adds a detailed explanation of the command.
- `commands:history` - Show the recent commands that were executed.

## Search Commands

- `search:file` - Shows a list of commands that fit the given file. [More information](search_file.md)
- `search:pattern` - Shows a list of commands that fit the given pattern.
- `search:tool` - Shows a list of commands that fit the given tool.


## Repository Commands

- `repository:list` - Shows a list of all registered repositories.
- `repository:create` - Creates a new repository and adds it (optional).
- `repository:register` - Add an existing repository to Forrest.
- `repository:remove` - Remove a specified installed repository.
- `repository:command:add` - Add a new command to the given repository.
- `repository:command:remoce` - Remove a command from the given repository. 

## Directory Commands

If you want to learn more about the directories and how to create your own private directory please read the chapter about "[Forrest Directories](../directories/directories.md)"


- `directory:list` - List all repositories from the Forrest directory.
- `directory:install` - Install a specified repository from the Forrest directory.
- `directory:import` - Import an existing directory.
- `directory:export` - Export an existing directory.
