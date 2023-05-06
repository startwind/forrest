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

- **runnable** [default: true] - if set to false Forrest will only show the command and not run it. This is the case if a command can be harmful or it does not return. *IMPORTANT: the parameters identifier must correspond to the parameter in the prompt.*

- **parameters** [optional] - The parameter field helps specifying and validating the parameter. It can also help predefine values or define enums. 
  - **name** - The name of the parameter.
  - **description** - The description of the parameter. Will be shown when the user has to enter the value.
  - **default** - The default value for this parameter.
  - **type** - The type of the value. This will help validating the parameter and will provide new functionality based on the type. 
  - **file-formats** - The file format is only relevant if the type is `forrest_filename`. This field is also used for the reverse command search via `search:file`. 
