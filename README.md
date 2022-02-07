# What is Changelager?
It was a project inspired by a [Jack McDade Tweet](https://twitter.com/jackmcdade/status/1004788998127128581) this is changelogs as a service. 

### How does it work?
It uses deploy keys with write access to push tags to any git host. Anywhere? Yep, literally any git host that's accessable to where you host this application.

By default it uses the commits since the last release to build the changelog, but the messages can be re-written.

### Why deploy keys?
The first version of changelager use the github, gitlab, and bitbucket APIs to manage official releases. However, it required a lot of work to implement since it was just me working on them, and I didn't regularly use gitlab or bitbucket so they were never fully developed. Using git's tags, and managing the release data separate allows for much more versatility, not to mention it being significantly easier to maintain.

# Installation
This app requires docker and docker-compose, then uses laravel sail to serve itself.
```
./sail up -d
./sail art key:gen
npm install 
npm run production
./sail art migrate
```
Once sail is up, and not producing any errors you're all set.

Please [report any issues](https://github.com/austinkregel/changelager/issues)
