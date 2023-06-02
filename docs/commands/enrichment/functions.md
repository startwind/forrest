# Functions

Functions are an easy way to enrich commands with dynamic data. 

> **_NOTE:_**  If you are missing a function for your prompts feel free to create an issue, and we will try to implement it. 

## `date()`

The date function injects the current date in any needed format. As format we have chosen the [PHP date format](https://www.php.net/manual/en/datetime.format.php).

### Example

This prompt will echo the current date in the `YYYY-MM-DD` format (e.g. 2025-01-20). 

```yaml
prompt: echo ${date(Y-m-d)}
```


## `env()`

The env function injects an environment variable from your system

### Example

This example will add the `LOGNAME` from the system as a default value.

```yaml
parameters:
  user_name:
    default: ${env(LOGNAME)}
```

## `docker-names()`

The `docker-names()` function will list all currently running docker containers by name. 

### Example

```yaml
commands:
  'enum-explode':
    name: 'docker:ssh'
    description: 'Show prompt for login'
    runnable: false
    prompt: 'docker exec -it ${docker_name} /bin/bash'
    parameters:
      docker_name:
        enum: "${docker-names()}"
```

## `docker-images()`

The `docker-images()` function will list all currently running docker containers by image name.

### Example

```yaml
commands:
  'enum-explode':
    name: 'docker:ssh'
    description: 'Show prompt for login'
    runnable: false
    prompt: 'docker exec -it ${docker_image} /bin/bash'
    parameters:
      docker_image:
        enum: "${docker-images()}"
```
