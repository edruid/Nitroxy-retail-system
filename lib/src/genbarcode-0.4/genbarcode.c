/*
 
 * Genbarcode 0.4
 
 * Genbarcode can't generate Barcode-Images, but a string which can
 * be used by image-creators, such as Folke Ashbergs PHP-Barcode
 
 * Encoding is done using GNU-Barcode (libbarcode), which can be found
 * at http://www.gnu.org/software/barcode/
   
 * (C) 2001,2002,2003 by Folke Ashberg <folke@ashberg.de>
 
 * The newest Version can be found at http://www.ashberg.de/bar
 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.

 */

#include <stdio.h>
#include <stdlib.h>
#include <string.h>

#include "barcode.h"

#ifdef STRICMP
#define strcasecmp stricmp
#endif

void usage(void){
    fprintf(stderr,
		"Genbarcode 0.4, Copyright (C) 2001,2002,2003 by Folke Ashberg\n"
		"Genbarcode comes with ABSOLUTELY NO WARRANTY.\n"
		"\n"
		"Usage: genbarcode <code> [<encoding>]\n"
		"\n"
		"You can use the following types:\n"
		"ANY    choose best-fit (default)\n"
		"EAN    8 or 13 EAN-Code\n"
		"UPC    12-digit EAN \n"
		"ISBN   isbn numbers (still EAN-13) \n"
		"39     code 39 \n"
		"128    code 128 (a,b,c: autoselection) \n"
		"128C   code 128 (compact form for digits)\n"
		"128B   code 128, full printable ascii \n"
		"I25    interleaved 2 of 5 (only digits) \n"
		"128RAW Raw code 128 (by Leonid A. Broukhis)\n"
		"CBR    Codabar (by Leonid A. Broukhis) \n"
		"MSI    MSI (by Leonid A. Broukhis) \n"
		"PLS    Plessey (by Leonid A. Broukhis)\n"
#if BARCODE_VERSION_INT >= 9700
		"93     code 93 (by Nathan D. Holmes)\n"
#endif
	   );
}

int main(int argc, char **argv)
{
    struct Barcode_Item * bc;
    int bar;
    if (argc<2 || argc>3){
	usage();
	return 1;
    }
    if (argc==3){
	if (!strcasecmp(argv[2], "ANY")) bar=BARCODE_ANY;
	else if (!strcasecmp(argv[2], "EAN")) bar=BARCODE_EAN;
	else if (!strcasecmp(argv[2], "UPC")) bar=BARCODE_UPC;
	else if (!strcasecmp(argv[2], "ISBN")) bar=BARCODE_ISBN;
	else if (!strcasecmp(argv[2], "39")) bar=BARCODE_39;
	else if (!strcasecmp(argv[2], "128")) bar=BARCODE_128;
	else if (!strcasecmp(argv[2], "128C")) bar=BARCODE_128C;
	else if (!strcasecmp(argv[2], "128B")) bar=BARCODE_128B;
	else if (!strcasecmp(argv[2], "I25")) bar=BARCODE_I25;
	else if (!strcasecmp(argv[2], "128RAW")) bar=BARCODE_128RAW;
	else if (!strcasecmp(argv[2], "CBR")) bar=BARCODE_CBR;
	else if (!strcasecmp(argv[2], "MSI")) bar=BARCODE_MSI;
	else if (!strcasecmp(argv[2], "PLS")) bar=BARCODE_PLS;
#if BARCODE_VERSION_INT >= 9700
	else if (!strcasecmp(argv[2], "93")) bar=BARCODE_93;
#endif
	else {
	    usage();
	    return 1;
	}
    } else {
	bar=BARCODE_ANY;
    }
    
    bc=Barcode_Create(argv[1]);
    if (bc!=NULL){
	Barcode_Encode(bc, bar );
	if (bc->error==0){
	    printf("%s\n", bc->partial);
	    printf("%s\n", bc->textinfo);
	    printf("%s\n", bc->encoding);

		    
	} else {
	    fprintf(stderr, "Encoding Error\n");
	    return 1;
	}
	Barcode_Delete(bc);
    } else {
	fprintf(stderr, "Cannot create barcode\n");
	return 1;
    }

    return 0;
}







