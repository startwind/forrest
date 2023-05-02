# Creating a custom repository

Commands are divided into repositories in Forrest. We recommend that these are structured thematically and thus also grouped. This facilitates later administration and use.

When creating local repositories we recommend to choose our [YAML format](formats/yaml-format.md).

## Local repository

In the beginning, you should always start with a local and therefore private repository. Here, you can create and manage your own commands without affecting other users.

For the creation, Forrest provides the appropriate commands.

```shell
forrest.phar repository:create ~/local-forrest.yml
```

Once the repository is created, you can register it. Normally you will be asked if you want to add it right after the creation. In most cases this makes sense.

Newly created repositories always come with a default command that can be customized or deleted. Their content looks similar to this:

```yaml
repository:
    name: 'local commands'
    description: 'Commands for local usage'
    identifier: local-commands
    
commands:
    my-unique-command-name:
        name: 'foo:bar'
        description: 'Command description'
        prompt: 'ls -lah'
```

For adding new commands please have a look at the [yaml format description](formats/yaml-format.md).

If you did not register the repository on creation you can use the `repository:register` command.

````shell
forrest.phar repository:create ~/local-forrest.yml
````

## Remote repository

If you want to share a repository with the commands it contains with colleagues and others, it makes sense to store this repository on a server that is accessible to everyone.

The easiest way, and the one we recommend, is to do this via GitHub. In principle, it is sufficient to upload the locally created repository here. Instead of a local file, you then specify the `raw url`.

This looks similar to this:
`https://raw.githubusercontent.com/startwind/forrest-directory/main/repositories/friends-of-linux.yml`

In this case the command line to register that repository is:

```shell
forrest.phar repository:create https://raw.githubusercontent.com/startwind/forrest-directory/main/repositories/friends-of-linux.yml
```

Afterwards everybody who know the URL can use the commands. 
