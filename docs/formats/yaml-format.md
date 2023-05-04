# The YAML repository format

The YAML format is the default format for Forrest. If possible use this because the Forrest team maintains the YAML adapter. This way you can be sure that all new functionality is supported.

## Example

```yaml
commands:
  
  startServer:
    name: "server:start"
    description: "Starting the built-in web server"
    prompt: "php -S localhost:8000"
    
  memoryLimit:
    name: "run:memory:limit"
    description: "Run a php CLI command with a defined memory limit"
    prompt: "php -d memory_limit=${limit_in_megabyte}M ${filename}"

  worker-shell:
    name: 'docker:shell'
    description: 'Login into the docker container'
    runnable: false
    prompt: 'docker exec -it worker /bin/bash'
```

## Command fields

- **runnable** [default: true] - if set to false Forrest will only show the command and not run it. This is the case if a command can be harmful or it does not return. 
