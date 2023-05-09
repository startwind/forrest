# Forrest is awesome

Forrest seems to be a complex tool and very technical. That is wrong. But the easiest way to explain the awesomeness of this tool is by providing some real world and every day examples. 

## Remember **your** commands

As a developer everybody has some commands that are needed from time to time. Hopefully they are still in history when needed. If not, we have to look into our cheat sheet. 

```shell
forrest commands:list my-local-commands
```

This prompt will show all your locally stored commands that you added before.

## Remember your teams commands

Repositories that contain your commands are not limited to your local machine. You can just put them into your (private) GitHub repository for example. Those can then be added by every team member. And booom. Shared commands. The best thing is, that everytime you add a new command, the whole team benefits.

```shell
forrest repository:register https://raw.githubusercontent.com/my-project/forrest-repository/main/devops.yml
```

## Remember all commands

Yours and your teams commands may be very individual, but there are a lot of common commands out there everybody benefits from. Let it be the Linux find command in all its variation. We want Forrest to be the largest collection of those commands. 

As this list is very long we implemented a search on it. 

````shell
forrest search:pattern "decompress tar file"
````

## Find out what you can do

This is our teams favourite command. We call it the reverse search and it's unique. It's a very easy way find all commands registered for a given file type. So for example if you have a `tar` archive and don't know what to do with it or hpw to unpack just run:

````shell
forrest search:file archive.tar
````

The tool will list all registered commands for this file. Including decompressing it. 

## Future ready

These examples are already pretty awesome. [But the future will bring more](roadmap.md).
