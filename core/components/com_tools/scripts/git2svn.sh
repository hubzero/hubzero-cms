#!/bin/bash
#
# derived from https://github.com/guilhermechapiewski/git2svn
#
# git2svn.sh \
#   -g https://github.com/user/myrepo.git \
#   -s https://hubzero.org/tools/myrepo/svn/trunk \
#   -c /www/hub
#
# if an svn repository url is not provided, but a repo name is,
# then use hubconfiguration.php to guess at the svn repository url
# git2svn.sh \
#   -g https://github.com/user/myrepo.git \
#   -s myrepo
#   -c /www/hub


function debug {
    if [[ "${verbose}" == "True" ]] ; then
        echo "$1";
    fi
}

function error {
    echo "ERROR: $1";
    exit 1;
}

function svn_checkin {
    debug '... adding files to the Subversion repository';
    # keep looking until we have taken care of all 
    while [ `svn st ${svn_dir} | awk -F" " '{print $1 "|" $2}' | grep '[?!~]|' | wc -l` != "0" ] ; do
        debug "`svn status ${svn_dir}`"
        for file in `svn st ${svn_dir} | awk -F" " '{print $1 "|" $2}'`; do
            fstatus=`echo ${file} | cut -d"|" -f1`;
            fname=`echo ${file} | cut -d"|" -f2`;

            if [ "${fstatus}" == "?" ]; then
                if [[ "${fname}" == *@* ]]; then
                    svn ${svn_opts} add $fname@;
                else
                    svn ${svn_opts} add ${fname};
                fi
            fi
            if [ "${fstatus}" == "!" ]; then
                if [[ "${fname}" == *@* ]]; then
                    svn ${svn_opts} rm $fname@;
                else
                    svn ${svn_opts} rm ${fname};
                fi
            fi
            if [ "${fstatus}" == "~" ]; then
                # otherwise, remove the file and 
                # pull the old file from the svn repo
                rm -rf ${fname};
                svn ${svn_opts} up ${fname};
            fi
        done
    done
    debug '... finished adding files to the Subversion repository';
}

function svn_commit {
    debug "... committing to Subversion -> [${author}]: ${msg}";
    cd ${svn_dir} && svn ${svn_opts} ${svn_auth} commit -m "[${author}]: ${msg}" && cd ${base_dir};
    debug '... committed!';
}

function svn_clear_repo {
    debug "... clearing the Subversion repository";
    cd ${svn_dir};
    for file in `svn ls` ; do
        svn ${svn_opts} remove ${file};
    done
    unset file;
    cd ${base_dir};
}

function cleanup_repos {
    debug "cleaning up temporary directory ${repos_dir}";
    rm -rf ${repos_dir};
}

git_repo_url="";
svn_repo_url="";
hubconfig=".";
verbose="False";
t_repos_base="${HOME}";
svn_opt_quiet="-q";
svn_opts="";
git_opt_quiet="-q";
git_opts="";
options=":c:g:p:r:s:v";

# parse the command line flags and options
# separate flags from options

let nNamedArgs=0
let nUnnamedArgs=0
while (( "$#" ))
do
   case $1 in
      -v )
           namedArgs[${nNamedArgs}]=$1
           let nNamedArgs++
           shift
           ;;
      -* )
           namedArgs[${nNamedArgs}]=$1
           let nNamedArgs++
           shift
           namedArgs[${nNamedArgs}]=$1
           let nNamedArgs++
           shift
           ;;
       * )
           unnamedArgs[${nUnnamedArgs}]=$1
           let nUnnamedArgs++
           shift
           ;;
   esac
done

while getopts "${options}" Option "${namedArgs[@]}"
do
   case ${Option} in
      c ) hubconfig=${OPTARG};;
      g ) git_repo_url=${OPTARG};;
      r ) t_repos_base=${OPTARG};;
      s ) svn_repo_url=${OPTARG};;
      v ) verbose="True";;
   esac
done


# exit immediately on error
set -e

# add quiet flag (-q) to subversion operations
if [[ "${verbose}" == "False" ]] ; then
    svn_opts="${svn_opts} ${svn_opt_quiet}";
    git_opts="${git_opts} ${git_opt_quiet}";
fi

# input validation

# regexp only works with ascii urls
url_regex='^https?://[-A-Za-z0-9\+&@#/%?=~_|!:,.;]*[-A-Za-z0-9\+&@#/%=~_|]$';
proj_regex='^[-A-Za-z0-9\+@#/%?=~_|!:,.]+$';

if [[ ! -d "${t_repos_base}" ]] ; then
    error "Temporary repository directory \"${t_repos_base}\" does not exist.";
fi

if [[ ! -r "${hubconfig}/hubconfiguration.php" ]] ; then
    error "Hub configuration file \"${hubconfig}/hubconfiguration.php\" is not readable";
else
    hubconfig="${hubconfig}/hubconfiguration.php";
fi

if [[ ! "${git_repo_url}" =~ ${url_regex} ]] ; then
    error "Git repository does not look like a url: ${git_repo_url}";
fi

if [[ ! "${svn_repo_url}" =~ ${url_regex} ]] ; then
    if [[ ! "${svn_repo_url}" =~ ${proj_regex} ]] ; then
        error "Subversion repository does not look like a url: ${svn_repo_url}";
    else
        # looks like the svn_repo_url is a project name
        # try to guess the repository name from forgeURL

        forgeURL=`grep forgeURL ${hubconfig} | sed -n "s/^.*'\(.*\)'.*;/\1/p"`;
        svn_repo_url="${forgeURL}/tools/${svn_repo_url}/svn/trunk";
    fi
fi

# grab the svn username and password from the hub configuration file.
# these regexps work as long as the strings are single quoted.
svn_user=`grep svn_user ${hubconfig} | sed -n "s/^.*'\(.*\)'.*;/\1/p"`;
svn_pass=`grep svn_password ${hubconfig} | sed -n "s/^.*'\(.*\)'.*;/\1/p"`;
svn_auth="--username ${svn_user} --password ${svn_pass}";

base_dir=`pwd`;
repos_dir=`mktemp -d -p ${t_repos_base} git2svn-tmp-XXXXXXXXXX`;
git_dir="${repos_dir}/git.repo";
svn_dir="${repos_dir}/svn.repo";

# clone the git repository
debug "cloning the Git repository ${git_repo_url}";
git clone ${git_opts} "${git_repo_url}" ${git_dir};

# check out the svn repository
debug "checking out the Subversion repository ${svn_repo_url}";
svn ${svn_opts} checkout "${svn_repo_url}" ${svn_dir};

# find the latest commit in the git repo
commit=`cd ${git_dir} && git rev-list --all -n 1 && cd ${base_dir}`;

# Checkout the current commit on git
debug "... checking out Git commit ${commit}";
cd ${git_dir} && git checkout ${git_opts} ${commit} && cd ${base_dir};

# check to see if the git commit hash in our svn repository
# matches the latest git commit hash
git_commit_file="${svn_dir}/.git-commit"
if [[ -r ${git_commit_file} ]] ; then
    if [[ "${commit}" == "`cat ${git_commit_file}`" ]] ; then
        # commit hashs match
        # we don't need to update the svn repository
        debug "Subversion repository already up to date.";

        # cleanup temp directories
        cleanup_repos;

        debug "exiting...";
        exit 0;
    fi
fi


# Delete everything from SVN and copy new files from Git
# with Subversion 1.7+ we can just remove the files from
# the directory and overwrite them with the files from the
# Git repository. With Subversion 1.6 and below, we would
# need to replace all of the .svn directories in the
# directories we deleted and track down files that were
# deleted from the Git repositories. It is easier to
# "svn remove" all files from Subversion repo and "svn add"
# the files back from the Git repo.
svn_clear_repo;
author=${svn_user};
msg="preparing repo for new version from git"
svn_commit;

debug "... copying files from Git to Subversion repository";
#rm -rf ${svn_dir}/*;
cp -prf ${git_dir}/* ${svn_dir}/;

# Keep the bare minimum directory structure for hubzero applications
mkdir -p ${svn_dir}/bin \
         ${svn_dir}/data \
         ${svn_dir}/doc \
         ${svn_dir}/examples \
         ${svn_dir}/middleware \
         ${svn_dir}/rappture \
         ${svn_dir}/src;

# update the git-commit hash in the svn repository
echo ${commit} > ${git_commit_file}

# Remove Git specific files from SVN
for ignorefile in `find ${svn_dir} | grep .git | grep .gitignore`;
do
    rm -rf ${ignorefile};
done

# Add new files to SVN commit
svn_checkin;

debug "querying author information from Git commit ${commit}";
author=`cd ${git_dir} && git log -n 1 --pretty=format:%an ${commit} && cd ${base_dir}`;
msg=`cd ${git_dir} && git log -n 1 --pretty=format:%s ${commit} && cd ${base_dir}`;
svn_commit;

# cleanup temp directories
cleanup_repos

debug "exiting...";
exit 0;
