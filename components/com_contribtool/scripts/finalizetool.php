#!/bin/sh
#<?php die('Restricted access');/*?>
#
# @package      hubzero-cms
# @file         components/com_contribtool/scripts/finalizetool.php
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
#    finalizetool ?flags? <project>
#
#    where ?flags? includes:
#      -root /where ...... install into this directory (default /apps)
#      -revision num ..... install a particular subversion revision
#                          from the trunk (default is latest revision)
#      -as current|dev ... install as current or dev version.  This
#                          sets a symbolic link to the installed
#                          version.  (default is current version)
#
#      -title toolname ... If specified, then use this as the official
#                          title of the tool.  The official title
#                          usually comes from the LDAP, and it gets
#                          substituted into the tool.xml file
#                          (if there is one).
#      -version vnum ..... If specified, then use this as the official
#                          version number for the tool.  The official
#                          version usually comes from the LDAP, and it
#                          gets substituted into the tool.xml file
#                          (if there is one).
#
#      -license f ........ File containing license text or "-" to read
#                          license text from stdin.  Text is saved to
#                          a file called "LICENSE.txt" in the source
#                          tarball produced by this script.
#
#      -hubdir f .. Load configuration information from this
#                          directory, assumed to be HubConfiguration Class
#                          with simple variable assignments.  In
#                          particular, we look for:
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

proc string_insert {string index text} {
    return "[string range $string 0 $index]$text[string range $string [expr {$index+1}] end]"
}

proc relocate {dir path newdir} {
    set relpath [string range $path [expr [string length $dir]+1] end]
    return [file join $newdir $relpath]
}

proc mkdir_ifneeded {dir} {
    set path ""
    foreach comp [file split $dir] {
        set path [file join $path $comp]
        if {![file exists $path]} {
            exit_on_error file mkdir $path
        }
    }
}

#
#----------------------------------------------------------------------
# Parse all command line options
#----------------------------------------------------------------------
#
array set options [list \
    -root /apps \
    -revision HEAD \
    -as current \
    -title "" \
    -version "" \
    -license "" \
    -configuration "" \
    -svnuser "" \
    -hubdir "" \
]
set flags "-root /apps -revision NNN -as dev|current -title name -version vnum -license file -configuration phpfile -svnuser name"

if {[llength $argv] == 0 || $argv == "--help"} {
    puts ""
    puts "USAGE:  finalizetool ?options? project"
    puts ""
    puts "  -root /apps ....... Install the tool in this directory."
    puts "  -revision HEAD .... Install this subversion revision of the tool."
    puts "  -as current ....... Create symbolic link as dev or current version."
    puts "  -title name ....... Official title of this tool for tool.xml"
    puts "  -version num ...... Official version of this tool for tool.xml"
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

    if {"" == $options(-svnuser)} {
        set options(-svnuser) $cfg(svn_user)
    }
    set options(-svnpw) $cfg(svn_password)

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
set tooldir [file join $options(-root) $project]
if {![file isdirectory $tooldir]} {
    puts stderr "== ERROR: Tool \"$project\" is not installed in $tooldir"
    puts stderr "          Run \"installtool\" first."
    exit 1
}
if {$options(-revision) == "HEAD"} {
    set max -1
    foreach dir [glob -nocomplain [file join $tooldir r*]] {
        if {[regexp {r([0-9]+)$} $dir match num]} {
            if {$num > $max} {
                set max $num
            }
        }
    }
    if {$max < 0} {
        puts stderr "== ERROR: can't find latest rXX directory in $tooldir."
        puts stderr "          You may need to run \"installtool\" first."
        exit 1
    }
    set rnum "r$max"
} elseif {[regexp {^[rR]?([0-9]+)$} $options(-revision) match num]} {
    set rnum "r$num"
} else {
    puts stderr "== ERROR: bad -revision value \"$options(-revision)\""
    puts stderr "          should be \"rNNN\" or \"HEAD\""
    exit 1
}
set targetdir [file join $options(-root) $project $rnum]

#
# Make sure that the revision directory exists.
#
if {![file isdirectory $targetdir]} {
    puts stderr "ERROR: target directory for revision $rnum not found"
    puts stderr "       looking for $targetdir"
    exit 1
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

#
# Get information about the repository, and export a version of the
# source code into a temp directory for packaging into a tarball below.
#
if {[catch {svn info $targetdir} info] != 0
      || [regexp {[\n\r]URL: +([^\n\r]+)[\n\r]} $info match repo] == 0
      || [regexp {[\n\r]Revision: +([0-9]+)[\n\r]} $info match rnum] == 0} {
    puts stderr "== ERROR: can't determine current revision in subversion."
    puts stderr $info
    exit 1
}

set tmpdir [file join /tmp src[pid]]
while {[file exists $tmpdir]} {
    append tmpdir x
}
set tardir [file join $tmpdir $project-r$rnum]
exit_on_error svn export --revision $rnum $repo $tardir

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

        # insert the given -version information
        if {"" != $options(-version)} {
            set update "<identifier>[xmlenc $options(-version)]</identifier>"

            foreach {v0 v1 text} [find_section \
                {<tool> <version> <identifier>} $info] break

            if {"" != $v0} {
                # replace <identifier> information
                set info [string replace $info $v0 $v1 $update]
            } else {
                # can't find <identifier>, so insert one at start of <version>
                foreach {i0 i1 vers} [find_section \
                    {<tool> <version>} $info] break
                if {"" != $i0} {
                    incr i0 9
                    set info [string_insert $info $i0 "$update\n"]
                } else {
                    puts "== ERROR: installtool goofed for the following file:"
                    puts "   $tfile"
                    puts "Can't find version section in XML to insert version tag \"$options(-version)\"."
                    puts "This might mean that \"installtool\" script was updated recently and this"
                    puts "tool was installed with an older version of the script."
                    puts "Maybe run \"installtool\" step again?"
                    exit 1
                }
            }
        }

        # find/replace the tool <name>
        if {"" != $options(-title)} {
            foreach {n0 n1 text} [find_section {<tool> <name>} $info] break
            set update "<name>$options(-title)</name>"
            if {"" != $n0} {
                set info [string replace $info $n0 $n1 $update]
            } else {
                set info [string_insert $info $t0 "  $update\n"]
            }
        }

        # find/replace the tool <id>
        foreach {i0 i1 text} [find_section {<tool> <id>} $info] break
        if {"" == $i0 || $text != $project} {
            set update "<id>$project</id>"
            if {"" != $i0} {
                puts "== ERROR: installtool goofed for the following file:"
                puts "   $tfile"
                puts "Expected tool name \"$project\" but found \"$text\"."
                puts "This might mean that \"installtool\" script was updated recently and this"
                puts "tool was installed with an older version of the script."
                puts "Maybe run \"installtool\" step again?"
                exit 1
            } else {
                puts "== ERROR: installtool goofed for the following file:"
                puts "   $tfile"
                puts "Expected tool name \"$project\" but found nothing."
                puts "This might mean that \"installtool\" script was updated recently and this"
                puts "tool was installed with an older version of the script."
                puts "Maybe run \"installtool\" step again?"
                exit 1
            }
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

        # copy the updated file into the exported src distribution
        file copy -force $tfile [relocate $targetdir $tfile $tardir]
    } else {
        puts stderr "== WARNING: Can't find <tool> section in $tfile"
        puts stderr "            Skipping tool.xml update"
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
    } else {
    	puts "== license licfile already exists =="
        if {[catch {exec diff $licfile $options(-license)} result]} {
            puts "== WARNING: license file exists and is different:"
            puts $result
            puts "== WARNING: replacing license file with current version"
            file copy -force $options(-license) $licfile
            svn commit $licfile -m "modified license terms in LICENSE.txt"
        }
    }
    file copy -force $options(-license) [relocate $targetdir $licfile $tardir]
    file delete -force $options(-license)
}

#
# Always close off permission to the "src" directory, and warn if
# we can't find it.  If the code is open source, people should be
# downloading the tarball or else checking out the code from subversion.
# Only the middleware needs the /apps stuff, and it doesn't need to
# access source code--only executables.
#
set srcdir [file join $targetdir src]
if {[file exists $srcdir]} {
    file attributes $srcdir -permissions go-rwx
} else {
    puts "== WARNING: can't find src directory in project $project"
    puts "== WARNING: Source code may be unprotected!"
}

#
# Always make a tarball for achival purposes and store it in the
# /tmp directory.  The calling code will copy the file back to a
# storage directory in the web area.  If the code is open source,
# then the web will allow access, and if it's closed source, the
# web will protect it.
#
set tarfile [file join /tmp $project-r$rnum.tar.gz]
file delete -force $tarfile
cd $tmpdir

if {[catch {exec tar cvzf $tarfile ./$project-r$rnum} result]} {
    puts stderr "== ERROR: tarball creation failed:"
    puts stderr "$result"
    exit 1
}

# clean up the src export
file delete -force $tmpdir

# return the name of the tarball for the database
puts "source tarball: $tarfile"
