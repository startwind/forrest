# The YAML repository format

The YAML format is the default format for Forrest. If possible use this because the Forrest team maintains the YAML adapter. This way you can be sure that all new functionality is supported.

## Example

```yaml
commands:
  
  startServer:
    name: "server:start"
    description: "Starting the built-in web server"
    prompt: "php -S localhost:${port}"
    parameters:
      port:
        default: 8000
    
  memoryLimit:
    name: "run:memory:limit"
    description: "Run a php CLI command with a defined memory limit"
    prompt: "php -d memory_limit=${limit_in_megabyte}M ${filename}"
    parameters:
      limit_in_megabyte:
        enum:
          - 16
          - 32
          - 65
          - 128
          - 256

  worker-shell:
    name: 'docker:shell'
    description: 'Login into the docker container'
    runnable: false
    prompt: 'docker exec -it worker /bin/bash'

  files-tar-decompress:
    name: 'files:tar:decompress'
    description: 'Decompress the given tar file'
    prompt: 'tar -zxvf ${filename}'
    parameters:
      filename:
        name: tar file
        description: Tar file that should be extracted
        type: forrest_filename
        file-formats:
          - tar.gz
          - tar
```

## Command fields

- **prompt** [required] The prompt is the most important and only required field. Basically it is the string that gets executed. There are many [possibilities to enrich the string](prompt.md). 


- **runnable** [default: true] - if set to false Forrest will only show the command and not run it. This is the case if a command can be harmful or it does not return. If it is not runnable the command will be copied to the clipboard. 


- **parameters** [optional] - The parameter field helps specifying and validating the parameter. It can also help predefine values or define enums. *IMPORTANT: the parameters identifier must correspond to the parameter in the prompt.*
  - **name** - The name of the parameter.
  - **description** - The description of the parameter. Will be shown when the user has to enter the value.
  - **default** - The default value for this parameter.
  - **output-format** -  This is an easy way to enrich the output. This is valuable for commands that only return a single output string like `wc -l` for example. [read more](#output-format--optional-)
  - **type** - The type of the value. This will help validating the parameter and will provide new functionality based on the type. [read more](#type--optional-)
  - **file-formats** - The file format is only relevant if the type is `forrest_filename`. This field is also used for the reverse command search via `search:file`. If the command takes a directory as parameter use `directory` as filetype.
  - **enum** You can define a list of values the user has to chose one from. [read more](#enum--optional-)
  - **enum-allow-custom** if set to true an option with the name <custom value> will be added. If this is selected the user can enter a custom value. Constraints still work with that combination.
  - **allowed-in-history** - If this field is set to false the command will not appear in the Forrest history. This can be important if some secret keys are included. If a parameter is type forrest_password this flag will automatically be set to false. 
  - **constraints** - Constraints are used to pre-validate parameters before the actual run. [read more](#constraints--optional-)

## Parameter

### `type` (optional)

Forrest supports some special parameter types. All types come with special control logic. 

- `forrest_filename`: A parameter that is defined as filename qualifies for the reverse search `search:file` as soon the `file-formats` fit.


- `forrest_password`: If a parameter is defined as a password the prompt will not show password in plain test, and it will also not be stored in history. 

### `output-format` (optional)

This is an easy way to enrich the output. This is valuable for commands that only return a single output string and is *optional*. Internally we use [`sprintf`](https://www.php.net/manual/en/function.sprintf.php) for the formatting. 

#### Example
```yaml
'file:length':
    name: 'file:length'
    description: 'Return the number of lines of a given file.'
    output-format: The file has %s lines.
    prompt: 'cat ${filename} | wc -l'
```

### `enum` (optional)

Enums can be used to predefine values the user can choose from. There are two ways of using the enums. The easy way is to just define a list of values. The second possibility is to define a set of key-value-pairs. The key is the value the user will see and therefore should be human-readable.The value is the actual value that will be put into the prompt.

#### Example
 ```yaml
'enum-key-values':
   name: 'parameters:enum:with-key'
   description: 'Check password handling'
   prompt: 'ls ${enum}'
   parameters:
     enum:
        enum:
          eins: one
          zwei: two
          drei: three
```
### `constraints` (optional)

Constraints are used to pre-validate parameters before the actual run. For example they can make sure that a parameter is a number or not empty.

By default the active constraints for every parameter include the `not-empty` constraint. If the parameter is allowed to be empty you have to overwrite the constraints.

#### Valid constraints

- `integer`: checks if the given value is a number (integer).
- `not-empty`: checks if the given value is not empty.
- `file-exists`: checks if a file exists.
- `file-not-exists`: checks if a file does not exist.
- `identifer`: checks if a string only contains lowercase letters or numbers.

#### Example

```yaml
"files:find:size":
  name: 'files:find:size'
  description: 'Command description'
  prompt: 'find . -type f -size +${size_in_mega_byte}M$'
  parameters:
    size_in_mega_byte:
      constraints:
        - integer
        - not-empty
```
