#!/bin/sh
#<?php die('Restricted access');?>
#
# @package		HUBzero CMS
# @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
# @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
#
# Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
# All rights reserved.
#
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License,
# version 2 as published by the Free Software Foundation.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
#
#----------------------------------------------------------------------
#  USAGE:
#    installtool ?flags? <project>
#
#    where ?flags? includes:
#      -hub nanohub.org .. doing install for this hub
#      -root /where ...... install into this directory (default /apps)
#      -revision num ..... install a particular subversion revision
#                          from the trunk (default is latest revision)
#      -from branch ...... install a particular tag or branch (default
#                          is the trunk)
#      -as current|dev ... install as current or dev version.  This
#                          sets a symbolic link to the installed
#                          version.  (default is no link)
#
#      -type app ......... Type of project--either "app" or "raw".
#                          App projects are all named "app-project".
#                          For raw projects, the project name is used
#                          directly.
#
#      -hubdir f .. Load configuration information from this
#                          directory, assumed to be HubConfiguration Class
#                          with simple variable assignments.  In
#                          particular, we look for:
#                            $forgeURL ......... xhub.org
#                            $svn_user ....... user for subversion
#                            $svn_password ... passwd for subversion
#
#      -svnuser name ..... Overrides the $svn_user value.
#                          Sets the username used to check out code
#                          from Subversion.
#
#----------------------------------------------------------------------
#\
if [ $(whoami) != apps ]; then exec sudo -u apps expect $0 "$@";
#\
else exec expect $0 "$@"; fi
#----------------------------------------------------------------------
# expect interprets everything from here on...

# this is an easy way to exec commands and catch errors:
proc exit_on_error {args} {
    if {[catch {eval $args} result]} {
        puts stderr "ERROR!"
        puts stderr $result
        exit 1
    }
    return $result
}

# use this to exec subversion commands:
proc svn {args} {
    global options
    set timeout 300 ;# timeout for expect
    log_user 0      ;# change to 1 for debugging

    set cmd [lindex $args 0]
    if {[lsearch {checkout co update list info} $cmd] >= 0} {
        lappend args --no-auth-cache
        if {"" != $options(-svnuser)} {
            lappend args --username $options(-svnuser)
        }
    }

    set result ""
    eval exp_spawn svn $args
    expect {
        -re "Password( for '.*')?:" {
            if {![info exists options(-svnpw)] ||
                  "" == $options(-svnpw)} {
                set old_timeout $timeout; set timeout -1
                if {![log_user]} {
                    send_user $expect_out(0,string)
                }
                stty -echo
                expect_user -re (.*)\n {
                    set options(-svnpw) $expect_out(1,string)
                }
                send_user "\n"
                stty echo
                set timeout $old_timeout
            }
            exp_send "$options(-svnpw)\r"
            exp_continue
        }
        "(t)emporarily?" {
            exp_send "t\r"
            exp_continue
        }
        timeout {
            error "timeout: no response from svn"
        }
        -re "(.*)\n" {
            append result $expect_out(0,string)
            exp_continue
        }
        eof {
            return $result
        }
    }
}

# use this to search for files such as tool.xml:
proc find_files {dir fname} {
    set dirlist $dir
    set found ""
    while {[llength $dirlist] > 0} {
        set dir [lindex $dirlist 0]
        set dirlist [lrange $dirlist 1 end]

        foreach path [glob -nocomplain [file join $dir *]] {
            if {[string equal [file tail $path] $fname]} {
                lappend found $path
            }
            if {[file isdirectory $path]} {
                lappend dirlist $path
            }
        }
    }
    return $found
}

proc find_section {tags text} {
    set tag [lindex $tags 0]
    set tags [lrange $tags 1 end]
    regsub ^< $tag </ tagend
    regsub ^> $tag /> tagalone

    if {[regexp -nocase -indices "${tag}(.*:?)${tagend}" $text match inner]} {
        foreach {m0 m1} $match break
        foreach {i0 i1} $inner break
        set subtext [string range $text $i0 $i1]
        if {[llength $tags] == 0} {
            return [list $m0 $m1 $subtext]
        }
        set s0 ""
        foreach {s0 s1 subtext} [find_section $tags $subtext] break
        if {"" == $s0} {
            return [list "" "" ""]
        }
        return [list [expr {$i0+$s0}] [expr {$i0+$s1}] $subtext]
    }
    if {[regexp -nocase -indices $tagalone $text match]} {
        foreach {m0 m1} $match break
        if {[llength $tags] == 0} {
            return [list $m0 $m1 ""]
        }
        return [list "" "" ""]  ;# nothing more we could possibly search for
    }
    return [list "" "" ""]
}

proc find_config {var info vname} {
    upvar $vname result
    set re [format {\$mosConfig_%s += +['"]([^'"]+)['"];} $var]
    if {[regexp $re $info match result]} {
        return 1
    }
    return 0
}

proc string_insert {string index text} {
    return "[string range $string 0 $index]$text[string range $string [expr {$index+1}] end]"
}

#
#----------------------------------------------------------------------
# Parse all command line options
#----------------------------------------------------------------------
#
array set options [list \
    -hub "" \
    -root /apps \
    -revision HEAD \
    -from trunk \
    -as dev \
    -type "app" \
    -configuration "" \
    -svnuser "" \
    -hubdir "" \
]
set flags "-hub domain -root /apps -revision NNN -from branch -as dev|current -type app -configuration phpfile -svnuser name"

if {[llength $argv] == 0 || $argv == "--help"} {
    puts ""
    puts "USAGE:  installtool ?options? project"
    puts ""
    puts "  -hub xhub.org ..... Doing install for this hub domain."
    puts "  -root /apps ....... Install the tool in this directory."
    puts "  -revision HEAD .... Install this subversion revision of the tool."
    puts "  -from trunk ....... Check out from trunk, branch, or tag."
    puts "  -type app ......... Type of the project either app- or raw name."
    puts "  -as dev ........... Create symbolic link as dev or current version."
    puts "  -configuration f .. File containing config info (configuration.php)"
    puts "  -svnuser name ..... Check out as this svn user"
    exit 1
}

while {[llength $argv] > 0} {
    set flag [lindex $argv 0]
    if {[string index $flag 0] != "-"} {
        break
    }
    if {[lsearch -exact {-h -help --help} $flag] >= 0} {
        puts stderr "usage: $argv0 ?$flags? project"
        exit 1
    }
    if {![info exists options($flag)]} {
        puts stderr "bad option \"$flag\""
        puts stderr "usage: $argv0 ?$flags? project"
        exit 1
    }
    if {[llength $argv] < 2} {
        puts stderr "missing value for option $flag"
        puts stderr "usage: $argv0 ?$flags? project"
        exit 1
    }
    set options($flag) [lindex $argv 1]
    set argv [lrange $argv 2 end]
}

# should be one value left -- for the project name
if {[llength $argv] != 1} {
    puts stderr "usage: $argv0 ?$flags? project"
    exit 1
}
set project [lindex $argv end]

###################### configuration scan (poor hack) ######################

if {![file exists $options(-hubdir)]} {
        puts stderr "ERROR: specified base directory does not exist"
        puts stderr " at $options(-hubdir)"
        exit 5
}

if { ![ catch { set fid [open [file join $options(-hubdir) "hubconfiguration.php"] r] } ] } {
while { [gets $fid line] >= 0 } { if { [regexp {^\s*class\s+HubConfig\s+\{\s*$} $line] } { break } }
while { [gets $fid line] >= 0 } {
        if { [regexp {^\s*\}\s*$} $line] } { break }
        set key ""
        set value ""
        regexp {^\s*var\s*\$(\w+)\s*=\s*"([^"\\]*(\\.[^"\\]*)*)"} $line -> key value
        if { [string compare $key {}] == 0 } { regexp {^\s*var\s*\$(\w+)\s*=\s*'([^'\\]*(\\.[^'\\]*)*)'} $line -> key value }
        if { [string compare $key {}] == 0 } { regexp {^\s*var\s*\$(\w+)\s*\=\s*(\w+)\s*\;\s*$} $line -> key value }
        set cfg($key) $value
}
close $fid
}

#
#----------------------------------------------------------------------
# Grab options from the hubconfiguration file, if there is one.
#----------------------------------------------------------------------
#

    if {"" == $options(-hub)} {
	set options(-hub) $cfg(forgeURL)
    }
    if {"" == $options(-svnuser)} {
	set options(-svnuser) $cfg(svn_user)
    }
    set options(-svnpw) $cfg(svn_password)

switch -- $options(-type) {
    app { set prefix "app-$project" }
    raw { set prefix $project }
    default {
      puts stderr "bad project type \"$options(-type)\": should be app or raw"
      exit 1
    }
}

#
# Build the name of the install directory.  If -revision is "HEAD"
# then figure out the revision number.  Otherwise, use it as-is.
# Build up a combination of the -root and -revision values into
# a name that looks like root/app-name/rXXX
#
set repo "$options(-hub)/tools/$prefix/svn/$options(-from)"
set rnum 0
if {$options(-revision) == "HEAD"} {
    if {[catch {svn info $repo} info] != 0} {
        puts stderr "error: can't determine current revision in subversion."
        puts stderr $info
        exit 1
    }
    #
    # Note:  Report the "Last Changed Rev" for the checked out dir.
    #   This is a more accurate reflection of the revision number.
    #   It may be smaller than the current revision if, for example,
    #   there have been revisions on other branches.  But it is an
    #   accurate indication of what revision matters for this part
    #   of the project.
    #
    if {![regexp {\nLast Changed Rev: +([0-9]+)} $info match rnum] || $rnum < 1} {
        puts stderr "error: can't determine current revision in subversion."
        puts stderr "got: $info"
        exit 1
    }
} else {
    if {[catch {svn info --revision $options(-revision) $repo} info] != 0} {
        puts stderr "error: can't find specified revision in subversion."
        puts stderr $info
        exit 1
    }
    if {![regexp {\nLast Changed Rev: +([0-9]+)} $info match rnum] || $rnum < 1} {
        puts stderr "error: can't determine specified revision in subversion."
        puts stderr "got: $info"
        exit 1
    }
}
set targetdir [file join $options(-root) $project "r$rnum"]

#
# Create the directory, if needed.  If the directory appears to
# have stuff in it, then "update" its contents.  Otherwise, do
# a "checkout" to get the contents.
#
proc mkdir_ifneeded {dir} {
    set path ""
    foreach comp [file split $dir] {
        set path [file join $path $comp]
        if {![file exists $path]} {
            exit_on_error file mkdir $path
        }
    }
}
mkdir_ifneeded $targetdir

if {[llength [glob -nocomplain [file join $targetdir *]]] == 0} {
    # nothing there -- do a checkout
    exit_on_error svn checkout --revision $rnum $repo $targetdir

    # directory had better be there now
    if {[llength [glob -nocomplain [file join $targetdir .svn]]] == 0} {
        puts "== ERROR: subversion checkout failed"
        exit 1
    }
} else {
    # something there -- do an update

    # if tool.xml exists, revert it first so it gets updates
    foreach tfile [find_files $targetdir tool.xml] {
        exit_on_error svn revert $tfile
    }
    exit_on_error svn update $targetdir
}

#
# Make a symbolic link to install this final version according
# to the -as argument.  If -as is "", then avoid making a link.
#
if {"" != $options(-as)} {
    set dir [file dirname $targetdir]
    set saveddir [pwd]
    cd $dir
    file delete -force $options(-as)
    exit_on_error exec ln -s [file tail $targetdir] $options(-as)
    cd $saveddir
}

#
# Look for an "invoke" script in the "middleware" directory,
# and if it's not there, create one automatically and print a
# warning about it.
#
if {![file exists [file join $targetdir middleware]]} {
    puts "== WARNING: Missing middleware directory."
} else {
    set invokescript [file join $targetdir middleware invoke]
    if {![file exists $invokescript]} {
        puts "== WARNING: Missing middleware/invoke script."
        if {[catch {
            set fid [open $invokescript w]
            puts $fid "#!/bin/sh\n"
            puts $fid "/apps/rappture/invoke_app \"$@\" -t $project"
            close $fid
            file attributes $invokescript -permissions ugo+rx
        } result] == 0} {
            puts "            Created default middleware/invoke script."
        } else {
            puts stderr "== ERROR: Attempt to create invoke script failed."
            puts stderr $result
        }
    }
}

#
# if a src directory exists hide it from the world. open source code is
# treated elsewhere
#
if {[file exists [file join $targetdir src]]} {
    file attributes [file join $targetdir src] -permissions go-srx
}

#
# If the distribution contains a tool.xml file, then get information
# about the current version and substitute it into the file.
#
proc xmlenc {str} {
    regsub -all {&} $str "\007" str
    regsub -all {<} $str {\&lt;} str
    regsub -all {>} $str {\&gt;} str
    regsub -all "\007" $str {\&amp;} str
    return $str
}

foreach tfile [find_files $targetdir tool.xml] {
    set svn(info) [exit_on_error svn info $targetdir]

    #
    # Note:  Report the "Last Changed Rev/Date" for the checked out
    #   directory.  This is a more accurate reflection of the revision
    #   number.  It may be smaller than the current revision if, for
    #   example, there have been revisions on other branches.  But it
    #   is an accurate indication of what revision matters for this
    #   part of the project.
    #
    if {![regexp {Last Changed Rev: *([0-9]+)} $svn(info) match svn(rev)]} {
        set svn(rev) ""
    }
    if {![regexp {Last Changed Date: *([^\r\n]+)} $svn(info) match svn(date)]} {
        set svn(date) ""
    }

    #
    # If we have all of the information, substitute into the tool.xml file.
    #
    if {"" != $svn(rev) && "" != $svn(date)} {
        if {[catch {
            set fid [open $tfile r]
            set info [read $fid]
            close $fid
        } result]} {
            puts stderr "can't read file $tfile"
            puts stderr $result
            exit 1
        }

        # find the start of the <tool> section
        foreach {t0 t1 text} [find_section <tool> $info] break
        if {"" != $t0} {
            incr t0 6

            set version "???"
            foreach {i0 i1 version} [find_section \
                {<tool> <version> <identifier>} $info] break

            set date [clock format [clock seconds] \
                -format "%Y-%m-%d %H:%M:%S %Z"]
            set update "<version>
    <identifier>[xmlenc $version]</identifier>
    <application>
      <revision>[xmlenc $svn(rev)]</revision>
      <modified>[xmlenc $svn(date)]</modified>
      <installed>[xmlenc $date]</installed>
      <directory id=\"top\">[xmlenc $targetdir]</directory>
      <directory id=\"tool\">[xmlenc [file dirname $tfile]]</directory>
    </application>
  </version>"

            foreach {v0 v1 text} [find_section {<tool> <version>} $info] break
            if {"" != $v0} {
                set info [string replace $info $v0 $v1 $update]
            } else {
                set info [string_insert $info $t0 "  $update\n"]
            }

            # find/replace the tool <name> -- use project name for now
            foreach {n0 n1 text} [find_section {<tool> <name>} $info] break
            set update "<name>$project</name>"
            if {"" != $n0} {
                set info [string replace $info $n0 $n1 $update]
            } else {
                set info [string_insert $info $t0 "  $update\n"]
            }

            # find/replace the tool <id>
            foreach {i0 i1 text} [find_section \
                {<tool> <id>} $info] break
            set update "<id>$project</id>"
            if {"" != $i0} {
                set info [string replace $info $i0 $i1 $update]
            } else {
                set info [string_insert $info $t0 "  $update\n"]
            }

            if {[catch {
                set fid [open $tfile w]
                puts -nonewline $fid $info
                close $fid
            } result]} {
                puts stderr $result
                puts stderr "while updating file $tfile"
                exit 1
            }
            puts "Updated $tfile"
        } else {
            puts stderr "WARNING: Can't find <tool> section in $tfile"
            puts stderr "         Skipping tool.xml update"
        }
    } else {
        puts stderr "WARNING: Can't find tool revision/date in subversion info."
        puts stderr "         Skipping tool.xml update"
    }
}

# return the current subversion revision number as the result
puts "installed revision: $rnum"
