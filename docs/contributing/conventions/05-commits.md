<!--
status: imported
source: https://help.hubzero.org/documentation/240/webdevs/conventions/commits
source-id: 3436
modified: 2017-12-20
imported: 2026-09-09
-->
# Commit Messages

GIT commit messages are generally comprised of three sections: a short summary, a longer explanation, and links to references. Starting with this structure forces you to answer why the change is necessary before outlining the changes that you have made.

- The summary should be no longer than 50 characters
- The summary should be tagged with what type of commit it is. For example, "[feat] Added a new feature" or "[fix] Fixed a bug".
- Each line of the explanation should wrap at 72 characters
- If a commit fixes an issue, it should be denoted with `Fixes: {url}`
- If a commit references another commit, outside source, etc, it should be denoted with `Refs: {url}`

The summary is required but the longer explanation and refs may not always be necessary or apply to the situation at hand.

### Template

You can tell GIT to use a template for commit messages with this structure. This is done by setting `commit.template` in `~/.gitconfig`:

```
[commit]
  template = ~/.gitmessage
```

Then create `~/.gitmessage` with the following:

```
#-----------------------50-----------------------|
#Summary (50 characters)

#----------------------------------72----------------------------------|
#Explanation (wrap around 72 characters (80 - short indent on either side))

#----------------------------------72----------------------------------|

#----------------------------------72----------------------------------|
#Refs:
#Fixes:
#----------------------------------72----------------------------------|
# Type can be
#    feat     (new feature)
#    fix      (bug fix)
#    refactor (refactoring production code)
#    style    (formatting, missing semi colons, etc; no code change)
#    docs     (changes to documentation)
#    test     (adding or refactoring tests; no production code change)
```
