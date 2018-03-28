# QUBESHub Instance of The HUBzero® Platform for Scientific Collaboration

**Main idea**:  The `app` directory is a subtree of both `dev` and the `master` branch, pointing to the `app` branch on GitHub.  The `dev` machine has `origin` set to `hubzero/hubzero-cms`, while the vagrant box has `origin` set to `qubeshub/hubzero-cms`.

# Development Workflow

The vagrant box pulls from this fork of the HubZero CMS.  So, if you are developing within the `app` directory and do not care about pushing changes to `dev` yet, you should be able to simply give the following:

```
git push origin master
```

Interesting stuff happens only when you want code pushed to (or from) `dev`.

## How to push changes to `dev`

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

# How to push changes from `dev`

If you are developing on `dev`, then NEVER push to `origin` (well, you probably won't be able to anyways).  In this case, everything should be subtree pushed, like this:

```
git subtree push --prefix=app qubeshub app
```

Then, in order for those changes to be integrated into the `master` branch, perform the following from the vagrant box:

```
git subtree pull --prefix=app origin app
```

Note that this is essentially the reverse workflow of working on the vagrant box.
