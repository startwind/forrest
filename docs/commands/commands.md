# Forrest CLI Commands 

## Commands

- `commands:list` - List all registered commands that can be run. [More information](docs/commands/commands_list.md)
- `commands:run` - Run a single command.  [More information](docs/commands/commands_run.md)
- `commands:show` - Show a single command with all its details and steps that will be executed.
- `commands:history` - Show the recent commands that were executed.

## Search Commands

- `search:file` - Shows a list of commands that fit the given file. [More information](docs/commands/search_file.md)
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

- `directory:list` - List all repositories from the Forrest directory.
- `directory:install` - Install a specified repository from the Forrest directory.
