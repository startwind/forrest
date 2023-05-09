# `commands:run`

This command runs the actual scripts. 

![search:file command](../images/commands_run.png)

## Parameters

## Options

- `--force`: If this parameter is added Forrest will not ask for confirmation before running the command. As this can be very insecure (some commands come directly from the cloud) the force option can only be applied if the command did not change since the last run. 


- `--parameters`, `-p`: 

## Example

```shell
forrest run forrest-linux:files:find:older
```
