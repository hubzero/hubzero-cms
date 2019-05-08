# QUBESHub Instance of The HUBzero® Platform for Scientific Collaboration

[![DOI](https://zenodo.org/badge/70513480.svg)](https://zenodo.org/badge/latestdoi/70513480)

All extensions of the `app` directory are subtrees (effectively).

## Components

 * [com_fmns](https://github.com/qubeshub/com_fmns): Faculty Mentoring Networks component (_in development_). **dev** :white_check_mark: **prod** :white_check_mark:
 * [com_partners](https://github.com/qubeshub/com_partners): Partners component **dev** :white_check_mark: **prod** :white_check_mark:
 * [com_publications](https://github.com/qubeshub/com_publications): **Override** of HubZero publications component. **dev** :white_check_mark: **prod** :white_check_mark:
 * [com_tags](https://github.com/qubeshub/com_tags): **Override** of HubZero tags component. **dev** :white_check_mark: **prod** :white_check_mark:

## Modules

Note:  Needed to include extra installation directory instructions in the `composer.json` files so that they install in the `mod_` prefix directories.

 * [mod_partners](https://github.com/qubeshub/mod_partners): Shows partners in grid format. **dev** :white_check_mark: **prod** :white_check_mark:
 * [mod_qubes_events](https://github.com/qubeshub/mod_qubes_events): Displays QUBES Happening events, consisting mainly of faculty mentoring networks. **dev** :white_check_mark: **prod** :white_check_mark:
 * [mod_qubes_tools](https://github.com/qubeshub/mod_qubes_tools): Displays QUBES Tools in grid format (_in development_). **dev** :x: **prod** :x:
 * [mod_showcase](https://github.com/qubeshub/mod_showcase): Displays static and dynamic billboards in grid format. **dev** :white_check_mark: **prod** :white_check_mark:
 * [mod_slider](https://github.com/qubeshub/mod_slider): Displays billboards in slider format (_not live on QUBES_) **dev** :white_check_mark: **prod** :white_check_mark:

## Plugins
 * [plg_content_qubesmacros](https://github.com/qubeshub/plg_content_qubesmacros): Content plugin that contains QUBES macros. **dev** :white_check_mark: **prod** :white_check_mark:
 * [plg_projects_publications](https://github.com/qubeshub/plg_projects_publications): **Override** of HubZero project publications plugin. **dev** :white_check_mark: **prod** :white_check_mark:
 * [plg_groups_publications](https://github.com/qubeshub/plg_groups_publications): Publication plugin for groups component. **dev** :white_check_mark: **prod** :x:
 * [plg_groups_resources](https://github.com/qubeshub/plg_groups_resources): **Override** of HubZero resource plugin for groups. *This will eventually take over the plg_groups_publications plugin above* **dev** :x: **prod** :x:
 * [plg_groups_usage](https://github.com/qubeshub/plg_groups_usage): **Override** of HubZero usage plugin for groups. **dev** :white_check_mark: **prod** :white_check_mark:
 * [plg_system_menurouter](https://github.com/qubeshub/plg_system_menurouter): Prepend menu parent items to generated component URLs and route menu items appropriately. **dev** :white_check_mark: **prod** :white_check_mark:
 * [plg_system_subnav](https://github.com/qubeshub/plg_system_subnav): Component/URL to subnavigation mapping **dev** :white_check_mark: **prod** :white_check_mark:

## Templates

 * [tpl_bmc](https://github.com/qubeshub/tpl_bmc): QUBES 2018 template **dev** :white_check_mark: **prod** :white_check_mark:
 * [tpl_qubes](https://github.com/qubeshub/tpl_qubes): QUBES 2015 template **dev** :white_check_mark: **prod** :white_check_mark:

# Development Workflow

The vagrant box pulls from this fork of the HubZero CMS.  So, if you are developing within the `app` directory and do not care about updating the servers (`dev`, `stage`, `qa`, or `prod`), you should be able to simply give the following:

```
git push origin master
```

Make sure before you push, though, to pull in changes first:

```
git pull --rebase origin master
```

Interesting stuff happens only when you want to update the code on the servers.

Note that in the commands below, while we are referring to "subtrees", we are not using the "subtree" scripts bundled with git.  This is due to (1) the complete clusterfudge that occurs with history when using subtrees, and (2) the occurrence of the [subtree-cache directory](https://github.com/dflydev/git-subsplit/issues/14) which gets HUGE and is never cleaned.  Also, going the manual route forces a deeper understanding of git, which overall HAS to be a good thing, right?

**Reference for manual subtree commands**:  [Mastering Git subtrees](https://medium.com/@porteneuve/mastering-git-subtrees-943d29a798ec) by [Christophe Porteneuve](https://medium.com/@porteneuve?source=post_header_lockup).

## Overriding a core extension

First, checkout a new branch:

```
[master]$ git checkout -b <extension>
```

Filter out the core extension from the history into the new branch:

```
[extension]$ git filter-branch --subdirectory-filter <path to core extension>
``` 

Add the new remote on GitHub:

```
[extension]$ git remote add <extension> https://github.com/qubeshub/<extension>.git
```

Push to the new remote repository:

```
[extension]$ git push -u <extension> <extension>:master
```

Switch back to the `master` branch and follow the instructions in the next section to add in the new remote extension as a subtree to the `app` directory.

```
[extension]$ git checkout master
```

## Adding remote extension as a subtree to this repository

First, add the remote repository and fetch the repo:

```
[master]$ git remote add <extension> https://github.com/qubeshub/<extension>
[master]$ git fetch <extension>
```

Now, pull in the extension repository and put it into the correct subdirectory of `app`.

```
[master]$ git read-tree --prefix=app/<extension dir> -u <extension>/master
```

`<extension dir>` can be
 * `app/components/<extension>`,
 * `app/modules/<extension>`,
 * `app/plugins/<extension>`,
 * `app/templates/<extension>`

The previous `read-tree` command will not commit the result - we'll have to do that ourselves.

```
[master]$ git commit -m "Added <extension> as subtree"
```

## Updating remote extensions

This repository is designed so you can code within multiple extensions at once, pushing and pulling from `origin`, without having to worry about updating the remote extensions, _unless you want to update the code on the servers_.  This section covers how to do just that.  We'll assume we want to update an extension called `ext_foo`.

For the code that follows, the `[main]$` prompt tells us we are on the main repo (this one), while the `[foo]$` prompt tells us we are on the master branch of `ext_foo`.

### Preliminary:  Make sure you have this extension setup as a remote:

```
[main]$ git remote
```

If you don't see it in the list, add it:

```
[main]$ git remote add ext_foo https://github.com/qubeshub/ext_foo.git
```

### Fetch remote data

First, `fetch` all remote changes.

```
[main]$ git fetch --all
```

You could only pull in the repo you want to update, which would just be

```
[main]$ git fetch <package>
```

### Get list of commits you want to backport to the package

This command will show all commits that touched a specific directory where our package resides (`app/ext/foo`).

```
[main]$ git log --oneline -- app/ext/foo
```

Grab the hashes for the commits of interest.

### Create a branch that tracks the master branch of `ext_foo`

```
[main]$ git checkout -b ext_foo ext_foo/master
```

### Begin to cherry-pick your commits

There are a few commands that we should be aware of:

 1. `git cherry-pick -x <hash>`
 2. `git cherry-pick -x -n (--no-commit) <hash>`
 3. `git cherry-pick -x --strategy=subtree <hash>`

It is a good idea to look at each commit to see if there was an edit, addition, or deletion, as the addition and deletions will cause some trouble.

**For most things, the 2nd command above will work.**  If you have files that are NOT in the extension of interest, use `git rm -r` (recursive for directories).

#### Only files were added in a commit

In this case, you need to use option #2 above.  Git doesn't know about these files yet in the package repo, and so it will add it to the package repo in the subdirectory structure of the main repo **_and then will automatically commit_**.  We need to manually tell git where the files should go:

```
[foo]$ git cherry-pick -x --no-commit <hash>
[foo]$ git status
...
Changes to be committed:
  (use "git reset HEAD <file>..." to unstage)

	new file:   app/ext/foo/<new file #1>
...
```

All we need to do now is a `git mv` for all new files

```
[foo]$ git mv app/ext/foo/<new file #1> <new file #1>
```

Finally, copy-and-paste the commit message from the main repository

```
[foo]$ git commit -m <commit message>
```

**Note**:  I'm curious of option #3 would help here, i.e. if it does a subtree merge, will it know to strip the `app/ext/foo`?  See next section note as well.

#### Only files were deleted in a commit

You might get the following:

```
[foo]$ git cherry-pick -x <hash>
error: could not apply <hash>... [Commit message here]
hint: after resolving the conflicts, mark the corrected paths
hint: with 'git add <paths>' or 'git rm <paths>'
hint: and commit the result with 'git commit'
```

```
On branch ext_foo
Your branch is ahead of 'ext_foo/master' by xx commits.
  (use "git push" to publish your local commits)

You are currently cherry-picking commit <hash>.
  (fix conflicts and run "git cherry-pick --continue")
  (use "git cherry-pick --abort" to cancel the cherry-pick operation)

Unmerged paths:
  (use "git add <file>..." to mark resolution)

	added by us:     <deleted file #1>
	added by us:     <deleted file #2>
```

Just `git rm` each file separately and then type `git cherry-pick --continue`.  

**Note**:  I'm pretty sure we can give strategy options here, such as `-Xtheirs`.  My impression here is that git sees that the extension has the files, but the main repo does not, and it doesn't know which one to respect.  By saying `theirs` we are telling git which one to do.

#### Modifications seen as deletion by ours?

Sometimes a commit with only edits comes across as a deletion.  For example,

```
You are currently cherry-picking commit <hash>.
  (fix conflicts and run "git cherry-pick --continue")
  (use "git cherry-pick --abort" to cancel the cherry-pick operation)

Changes to be committed:

	modified:   css/pages/resourceportal.css
	modified:   css/pages/resourceportal.scss

Unmerged paths:
  (use "git add/rm <file>..." as appropriate to mark resolution)

	deleted by us:   app/templates/bmc/css/pages/variables.scss
```

First, `mv` the file, as the `deleted by us` version is the correct version:

```
mv app/templates/bmc/css/pages/variables.scss css/pages/variables.scss
```

Note that `git mv` won't work as the `deleted by us` file is not under version control.  Now if you do `git status`, you might seen

```
...
Unmerged paths:
  (use "git add/rm <file>..." as appropriate to mark resolution)

	deleted by us:   app/templates/bmc/css/pages/variables.scss

Changes not staged for commit:
  (use "git add <file>..." to update what will be committed)
  (use "git checkout -- <file>..." to discard changes in working directory)

	modified:   css/pages/variables.scss
...
```

Strangely, we have to `git rm` the `app/templates/bmc` file and `git add` the modified file for staging.

```
[foo]$ git rm app/templates/bmc/css/pages/variables.scss
[foo]$ git add css/pages/variables.scss
```

We can then continue the cherry-picking
```
[foo]$ git cherry-pick --continue
```

### Push to remote after all cherry-picks

```
[foo]$ git push ext_foo HEAD:master
```

## How to push changes to `dev` (RARE)

_This is a rare operation as the only thing we are tracking in the app directory now are some overrides._

If you want to push changes to `dev`, you need to update the `app` repository, as the `dev` machine has the `app` directory added as a subtree.  To do this:

```
git subtree push --prefix=app origin app
```

This will update the `app` branch on GitHub, as long as `origin` is set to `https://github.com/qubeshub/hubzero.cms` (which it should be).

On the `dev` machine, all you have to do now is:

```
git subtree pull --prefix=app qubeshub app
```

Note the change of the remote from `origin` to `qubeshub`.  On `dev`, `origin` is set to `https://github.com/hubzero/hubzero.cms`, and `qubeshub` is set to `https://github.com/qubeshub/hubzero.cms`.

# Monthly updates

## Pull in code from upstream

On the Thursday or Friday before QA push, do the following:

```
git fetch --all
git pull upstream 2.2
```

This will pull in the new code into `/core`.  

## Update HubZero framework and run migration files in Vagrant Box

Make sure you are in the vagrant directory and run the following commands:

```
vagrant ssh
cd /var/www/public/core
php bin/composer install
cd ..
php muse migration -f
exit
```

## Update remote extensions

For the extensions that have overrides, follow the same steps above for updating remote extensions.

## Merge into app directory

After updating the remote extension, we need to merge those commits into the extensions in the app directory on the master branch.

```
git merge -X subtree=app/<extension directory> -Xtheirs --squash <extension>/master --allow-unrelated-histories
```

The `-Xtheirs` is important and safe, as we effectively want our version in the main repo to mirror the remote extension.

**This will pull in ALL commits in the history of the repo into the commit message.**  Edit the commit file as follows.

### Change the edit message

```
Squashed commit of the following:
```

to

```
[extension] Squashed commit of the following...
```

### Change the edit description

Include all recent commits from the core to app merge.  After the oldest more recent commit, delete all the way until the commit info at the end (although these will be stripped as they are just comments - good to reread just to be sure).

### Interactively rebase to merge all extensions updates into one commit

Supposing you just did 3 squash merges of updated extensions into the app directory, you now want to merge all of these into one commit by performing an interactive rebase as follows.

```
git rebase -i HEAD~3
```

This will put you into an editor and give you something like the following.

```
pick e43cebab [extension #1] Squashed commit of the following...
pick 56bcce71 [extension #2] Squashed commit of the following...
pick a2b6eecf [extension #3] Squashed commit of the following...
```

Change this to the following:

```
pick e43cebab [extension #1] Squashed commit of the following...
squash 56bcce71 [extension #2] Squashed commit of the following...
squash a2b6eecf [extension #3] Squashed commit of the following...
```

It will then open up the editor again to let you edit the commit message.  All commit messages for the three commits will be combined, giving you the option to create a new merged commit message.  Make this commit message the following:

```
[maint] April subtree merge of app extensions
```

For more info, check out the git docs:  https://git-scm.com/book/en/v2/Git-Tools-Rewriting-History

# Fixing core issues

Checkout a clone of `upstream/2.2`:

```
git checkout --track -b fix-core-bug upstream/2.2
```

Code the fix and then push the new branch to origin:

```
git push -u origin fix-core-bug
```

Then, do a pull request!  If you don't want to keep the local and/or remote branch:

```
git branch -D fix-core-bug // Delete locally
git push -d origin fix-core-bug // Delete remotely (I would wait to delete the remote branch until AFTER pull has occurred)
```
