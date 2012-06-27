#!/bin/sh
#<?php die('Restricted access');?>
#
# @package      hubzero-cms
# @file         components/com_contribtool/scripts/licensetool.php
# @author       Michael McLennan <mmclennan@purdue.edu>
# @author       Nicholas J. Kisseberth <nkissebe@purdue.edu>
# @copyright    Copyright (c) 2005-2011 Purdue University. All rights reserved.
# @license      http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
#
# Copyright (c) 2005-2011 Purdue University
# All rights reserved.
#
# This file is part of: The HUBzero(R) Platform for Scientific Collaboration
#
# The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
# software: you can redistribute it and/or modify it under the terms of
# the GNU Lesser General Public License as published by the Free Software
# Foundation, either version 3 of the License, or (at your option) any
# later version.
#
# HUBzero is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU Lesser General Public License for more details.
#
# You should have received a copy of the GNU Lesser General Public License
# along with this program.  If not, see <http://www.gnu.org/licenses/>.
#
# HUBzero is a registered trademark of Purdue University.
#
#----------------------------------------------------------------------
#  USAGE:
#    licensetool ?flags? <project>
#
#    where ?flags? includes:
#      -hub example.com .. doing install for this hub
#      -root /where ...... install into this directory (default /apps)
#      -revision num ..... install a particular subversion revision
#                          from the trunk (default is latest revision)
#      -from branch ...... install a particular tag or branch (default
#                          is the trunk)
#      -type app ......... Type of project--either "app" or "raw".
#                          App projects are all named "app-project".
#                          For raw projects, the project name is used
#                          directly.
#
#      -license f ........ File containing license text or "-" to read
#                          license text from stdin.  Text is saved to
#                          a file called "LICENSE.txt" in the source
#                          tarball produced by this script.
#
#      -configuration f .. Load configuration information from this
#                          PHP file, assumed to be a series of
#                          $mosConfig_* variable assignments.  In
#                          particular, we look for:
#                            $mosConfig_absolute_path .... /web/xhub
#
#      -svnuser name ..... Overrides the $mosConfig_svn_user value.
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
        puts stderr "== ERROR!"
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
    if {[lsearch {checkout co commit ci export update list info} $cmd] >= 0} {
        set args [linsert $args 1 --no-auth-cache]
        if {"" != $options(-svnuser)} {
            set args [linsert $args 1 --username $options(-svnuser)]
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

proc find_config {var info vname} {
    upvar $vname result
    set re [format {\$mosConfig_%s += +['"]([^'"]+)['"];} $var]
    if {[regexp $re $info match result]} {
        return 1
    }
    return 0
}

proc relocate {dir path newdir} {
    set relpath [string range $path [expr [string length $dir]+1] end]
    return [file join $newdir $relpath]
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
    -type "app" \
    -license "" \
    -configuration "" \
    -svnuser "" \
    -hubdir "" \
]
set flags "-hub domain -root /apps -revision NNN -from branch -type app -license file -configuration phpfile -svnuser name"

if {[llength $argv] == 0 || $argv == "--help"} {
    puts ""
    puts "USAGE:  licensetool ?options? project"
    puts ""
    puts "  -hub xhub.org ..... Doing install for this hub domain."
    puts "  -root /apps ....... Install the tool in this directory."
    puts "  -revision HEAD .... Install this subversion revision of the tool."
    puts "  -from trunk ....... Check out from trunk, branch, or tag."
    puts "  -type app ......... Type of the project either app- or raw name."
    puts "  -license file ..... Read license text from this file, or - for stdin"
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
# Get the text from the license file, if specified.
#
set fid ""
if {$options(-license) == "-"} {
    set fid stdin
} elseif {$options(-license) != ""} {
    set fid [open $options(-license) r]
}
if {$fid != ""} {
    set info [read $fid]
    if {$fid != "stdin"} {close $fid}
    set options(-license) [file join /tmp license[pid]]
    set fid [open $options(-license) w]
    puts -nonewline $fid $info
    close $fid
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

if {[file isdirectory $targetdir]} {
    # something there -- do an update

    # if tool.xml exists, revert it so there are no conflicts
    foreach tfile [find_files $targetdir tool.xml] {
        exit_on_error svn revert $tfile
    }
    exit_on_error svn update $targetdir
} else {
    append targetdir "-tmp"
    mkdir_ifneeded $targetdir
    exit_on_error svn checkout --revision $rnum $repo $targetdir

    # directory had better be there now
    if {[llength [glob -nocomplain [file join $targetdir .svn]]] == 0} {
        puts "== ERROR: subversion checkout failed"
        exit 1
    }
}

#
# If there's a license, then save the text into a file called
# "LICENSE.txt" in the main directory.  If the file already exists,
# then warn if it's not identical.
#
if {"" != $options(-license)} {
    set licfile [file join $targetdir LICENSE.txt]
    if {![file exists $licfile]} {
        file copy -force $options(-license) $licfile
        svn add $licfile
        svn commit $licfile -m "added license terms in LICENSE.txt"
        puts "added license file LICENSE.txt"
    } else {
        if {[catch {exec diff $licfile $options(-license)} result]} {
            file copy -force $options(-license) $licfile
            svn commit $licfile -m "modified license terms in LICENSE.txt"
            puts "modified license file LICENSE.txt"
        }
    }
    file delete -force $options(-license)
}

#
# If we had to check out a copy just to handle this operation, then
# remove the copy now.
#
if {[string match *-tmp $targetdir]} {
    exec rm -rf $targetdir
}
