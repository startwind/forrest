# `commands:list`

List commands that are registered in the current Forrest configuration.

By default only a subset of all Forrest commands are registered. To register more use the `directory:list` and `directory:install` command.

![search:file command](../images/commands_list.png)

## Arguments

- `repositoy` [optional] : You can use this parameter to show only the commands of one specified repository.

## Examples

List all commands that are currently registered in Forrest.
```shell
forrest commands:list
```

List all commands that are part of the given repository. (`forrest-linux`).
```shell
forrest commands:list forrest-linux
```
