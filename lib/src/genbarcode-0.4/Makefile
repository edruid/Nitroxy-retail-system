# 
# (C) 2001,2002,2003 by Folke Ashberg <folke@ashberg.de>
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.

PREFIX=/usr/local
CC=gcc
CFLAGS=-Wall	-I/usr/local/include
LIBS=-lbarcode	-L/usr/local/lib
INSTALL=/usr/bin/install

# only necessary for 'make dist'
DISTNAME="genbarcode-0.3"	    # example-0.1
DISTEXTRA="ChangeLog INSTALL README GPL"   # extra files to include in dist

PROGRAMS=genbarcode
S=genbarcode.c
OBS:=genbarcode.o

# don't remove
DISTNAME:=$(strip $(DISTNAME))
DISTEXTRA:=$(strip $(DISTEXTRA))
OBS:=$(filter %.o, $(OBS)) # ensure that only .o files in OBS

all: $(PROGRAMS)

info:
	@echo "PROGRAMS: $(PROGRAMS)"
	@echo "OBS: $(OBS)"

$(PROGRAMS): .dep $(OBS)
	@# generic for all binaries files.
	@# we will link all .o's into it, but if we have to build
	@# several binaries (all we can find in PROGRAMS), we have to
	@# filter-out first all other PROGRAM .o's 
	$(CC) -o $@ $@.o $(filter-out $(PROGRAMS:=.o), $(OBS)) $(LIBS)

.c.o:
	@# generic for all c .o
	$(CC) $(CFLAGS) -c -g $<

dep depend .dep:
	@echo "creating depencies"
	rm -f .tmp.dep
	@find . -name "*.c"   -maxdepth 1 -print0 | xargs -n 1 -0t $(CC) -M $(CFLAGS)  >>.tmp.dep
	mv .tmp.dep .dep
	    
install: $(PROGRAMS)
	@for prog in $(PROGRAMS) ; do \
	    echo "Installing $${prog} to $(PREFIX)/bin/"; \
	    $(INSTALL) -m 555 -s $${prog} $(PREFIX)/bin/ ; \
	done

clean:
	rm -f $(PROGRAMS) $(OBS) .dep

tags:
	@#VIM Users know why! *g*
	ctags --c-types=+c+p+f -R .
	
dist: .dep
	@if [ -z $(DISTNAME) ] ; then \
	    echo "You have to set DISTNAME  in Makefile first!" ; \
	    false ; \
	fi
	@if [ -e $(DISTNAME) ] ; then \
	    echo "$(DISTNAME) exits!" ;\
	    echo "You have to remove it because we need it temporarily!" ;\
	    false ; \
	fi
	@echo -n "Creating '$(DISTNAME).tar.gz'...."
	@mkdir -p $(DISTNAME)/
	@#Copy Sources and its depencies
	@for file in `\
	    ( \
	    find . -name "*.c"   -maxdepth 1 -print0 | xargs -n 1 -0t $(CC)  -MM $(CFLAGS)   ; \
	     ) 2>/dev/null | \
	    sed "s/^.*: \(.*\)/\1/" 2>/dev/null` ; do \
		cp $${file} $(DISTNAME)/ ; \
	done
	@#..and additional stuff
	@cp Makefile $(DISTNAME)/
	@for file in "$(DISTEXTRA)" ; do \
	    if [ -f "$${file}" ] ; then \
		cp "$${file}" $(DISTNAME) 2>/dev/null ;\
	    fi \
	done
	@tar cfz $(DISTNAME).tar.gz $(DISTNAME)
	@rm -rf $(DISTNAME)/
	@echo " should now exist"


# if .dep exists, we will include it
ifeq (.dep,$(wildcard .dep))
include .dep
endif
.PHONY: dep depend clean install all tags dist
