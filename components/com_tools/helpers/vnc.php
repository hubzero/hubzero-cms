<?php
/**
 * HUBzero CMS
 *
 * Copyright 2014 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

/*
 * This file was ported to PHP from the d3des.c file provided
 * with RealVNC 4.1.3. The code has been simplified for the 
 * sole purpose of encrypting/decrypting VNC passwords. Modified
 * to work with arithmetic (rather than logical) right shifts
 * for 32bit PHP implementations.
 *
 * The original and modified header comments are reproduced below.
 */

/*
 * This is D3DES (V5.09) by Richard Outerbridge with the double and
 * triple-length support removed for use in VNC.  Also the bytebit[] array
 * has been reversed so that the most significant bit in each byte of the
 * key is ignored, not the least significant.
 *
 * These changes are:
 *  Copyright (C) 1999 AT&T Laboratories Cambridge.  All Rights Reserved.
 *
 * This software is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/* D3DES (V5.09) -
 *
 * A portable, public domain, version of the Data Encryption Standard.
 *
 * Written with Symantec's THINK (Lightspeed) C by Richard Outerbridge.
 * Thanks to: Dan Hoey for his excellent Initial and Inverse permutation
 * code;  Jim Gillogly & Phil Karn for the DES key schedule code; Dennis
 * Ferguson, Eric Young and Dana How for comparing notes; and Ray Lau,
 * for humouring me on.
 *
 * Copyright (c) 1988,1989,1990,1991,1992 by Richard Outerbridge.
 * (GEnie : OUTER; CIS : [71755,204]) Graven Imagery, 1992.
 */

class ToolsHelperVnc
{
	public static function encrypt($data, $return_string = true)
	{
		$vnckey = array(23,82,107,6,35,78,88,7);

		if (is_string($data))
		{
			$data = array_values(unpack('C*',$data));
		}

		$result = self::crypt($data, $vnckey, False);

		if (!$return_string)
		{
			return $result;
		}
		else
		{
			return pack('C8', $result[0], $result[1], $result[2], $result[3],
			    $result[4], $result[5], $result[6], $result[7]);
		}
	}

	public static function decrypt($data, $return_string = true)
	{
		$vnckey = array(23,82,107,6,35,78,88,7);

		if (is_string($data))
		{
			$data = array_values(unpack('C*',$data));
		}

		$result = self::crypt($data, $vnckey, True);

		if (!$return_string)
		{
			return $result;
		}
		else
		{
			return pack('C8', $result[0], $result[1], $result[2], $result[3],
			    $result[4], $result[5], $result[6], $result[7]);
		}
	}

	private static function crypt($block, $key, $edf)
	{
		$block = array_pad($block,8,0);

		$bytebit = array(
			  1,   2,   4,   8, 
			 16,  32,  64, 128
			);

		$bigbyte = array(
		    0x00800000, 0x00400000,	0x00200000, 0x00100000,
		    0x00080000, 0x00040000,	0x00020000, 0x00010000,
		    0x00008000, 0x00004000,	0x00002000, 0x00001000,
		    0x00000800, 0x00000400,	0x00000200, 0x00000100,
		    0x00000080, 0x00000040,	0x00000020, 0x00000010,
		    0x00000008, 0x00000004,	0x00000002, 0x00000001
		    );

		/* Use the key schedule specified in the Standard (ANSI X3.92-1981). */

		$pc1 = array(
		    56, 48, 40, 32, 
		    24, 16,  8,  0, 
		    57, 49, 41, 33, 
		    25, 17,  9,  1, 
		    58, 50, 42, 34, 
		    26, 18, 10,  2, 
		    59, 51, 43, 35,
		    62, 54, 46, 38, 
		    30, 22, 14,  6, 
		    61, 53, 45, 37, 
		    29, 21, 13,  5, 
		    60, 52, 44, 36, 
		    28, 20, 12,  4, 
		    27, 19, 11,  3
		);

		$totrot = array( 
		     1,  2, 4,  6,
		     8, 10, 12, 14,
		    15, 17, 19, 21,
		    23, 25, 27, 28
		    );

		$pc2 = array(
		    13, 16, 10, 23,  
		     0,  4,  2, 27, 
		    14,  5, 20,  9,
		    22, 18, 11,  3, 
		    25,  7, 15,  6, 
		    26, 19, 12,  1,
		    40, 51, 30, 36, 
		    46, 54, 29, 39, 
		    50, 44, 32, 47,
		    43, 48, 38, 55, 
		    33, 52, 45, 41, 
		    49, 35, 28, 31
		    );

		$SP1 = array(
			0x01010400, 0x00000000, 0x00010000, 0x01010404,
			0x01010004, 0x00010404, 0x00000004, 0x00010000,
			0x00000400, 0x01010400, 0x01010404, 0x00000400,
			0x01000404, 0x01010004, 0x01000000, 0x00000004,
			0x00000404, 0x01000400, 0x01000400, 0x00010400,
			0x00010400, 0x01010000, 0x01010000, 0x01000404,
			0x00010004, 0x01000004, 0x01000004, 0x00010004,
			0x00000000, 0x00000404, 0x00010404, 0x01000000,
			0x00010000, 0x01010404, 0x00000004, 0x01010000,
			0x01010400, 0x01000000, 0x01000000, 0x00000400,
			0x01010004, 0x00010000, 0x00010400, 0x01000004,
			0x00000400, 0x00000004, 0x01000404, 0x00010404,
			0x01010404, 0x00010004, 0x01010000, 0x01000404,
			0x01000004, 0x00000404, 0x00010404, 0x01010400,
			0x00000404, 0x01000400, 0x01000400, 0x00000000,
			0x00010004, 0x00010400, 0x00000000, 0x01010004
			);

		$SP2 = array(
			0x80108020, 0x80008000, 0x00008000, 0x00108020,
			0x00100000, 0x00000020, 0x80100020, 0x80008020,
			0x80000020, 0x80108020, 0x80108000, 0x80000000,
			0x80008000, 0x00100000, 0x00000020, 0x80100020,
			0x00108000, 0x00100020, 0x80008020, 0x00000000,
			0x80000000, 0x00008000, 0x00108020, 0x80100000,
			0x00100020, 0x80000020, 0x00000000, 0x00108000,
			0x00008020, 0x80108000, 0x80100000, 0x00008020,
			0x00000000, 0x00108020, 0x80100020, 0x00100000,
			0x80008020, 0x80100000, 0x80108000, 0x00008000,
			0x80100000, 0x80008000, 0x00000020, 0x80108020,
			0x00108020, 0x00000020, 0x00008000, 0x80000000,
			0x00008020, 0x80108000, 0x00100000, 0x80000020,
			0x00100020, 0x80008020, 0x80000020, 0x00100020,
			0x00108000, 0x00000000, 0x80008000, 0x00008020,
			0x80000000, 0x80100020, 0x80108020, 0x00108000
			);

		$SP3 = array(
			0x00000208, 0x08020200, 0x00000000, 0x08020008,
			0x08000200, 0x00000000, 0x00020208, 0x08000200,
			0x00020008, 0x08000008, 0x08000008, 0x00020000,
			0x08020208, 0x00020008, 0x08020000, 0x00000208,
			0x08000000, 0x00000008, 0x08020200, 0x00000200,
			0x00020200, 0x08020000, 0x08020008, 0x00020208,
			0x08000208, 0x00020200, 0x00020000, 0x08000208,
			0x00000008, 0x08020208, 0x00000200, 0x08000000,
			0x08020200, 0x08000000, 0x00020008, 0x00000208,
			0x00020000, 0x08020200, 0x08000200, 0x00000000,
			0x00000200, 0x00020008, 0x08020208, 0x08000200,
			0x08000008, 0x00000200, 0x00000000, 0x08020008,
			0x08000208, 0x00020000, 0x08000000, 0x08020208,
			0x00000008, 0x00020208, 0x00020200, 0x08000008,
			0x08020000, 0x08000208, 0x00000208, 0x08020000,
			0x00020208, 0x00000008, 0x08020008, 0x00020200
			);

		$SP4 = array(
			0x00802001, 0x00002081, 0x00002081, 0x00000080,
			0x00802080, 0x00800081, 0x00800001, 0x00002001,
			0x00000000, 0x00802000, 0x00802000, 0x00802081,
			0x00000081, 0x00000000, 0x00800080, 0x00800001,
			0x00000001, 0x00002000, 0x00800000, 0x00802001,
			0x00000080, 0x00800000, 0x00002001, 0x00002080,
			0x00800081, 0x00000001, 0x00002080, 0x00800080,
			0x00002000, 0x00802080, 0x00802081, 0x00000081,
			0x00800080, 0x00800001, 0x00802000, 0x00802081,
			0x00000081, 0x00000000, 0x00000000, 0x00802000,
			0x00002080, 0x00800080, 0x00800081, 0x00000001,
			0x00802001, 0x00002081, 0x00002081, 0x00000080,
			0x00802081, 0x00000081, 0x00000001, 0x00002000,
			0x00800001, 0x00002001, 0x00802080, 0x00800081,
			0x00002001, 0x00002080, 0x00800000, 0x00802001,
			0x00000080, 0x00800000, 0x00002000, 0x00802080
			);

		$SP5 = array(
			0x00000100, 0x02080100, 0x02080000, 0x42000100,
			0x00080000, 0x00000100, 0x40000000, 0x02080000,
			0x40080100, 0x00080000, 0x02000100, 0x40080100,
			0x42000100, 0x42080000, 0x00080100, 0x40000000,
			0x02000000, 0x40080000, 0x40080000, 0x00000000,
			0x40000100, 0x42080100, 0x42080100, 0x02000100,
			0x42080000, 0x40000100, 0x00000000, 0x42000000,
			0x02080100, 0x02000000, 0x42000000, 0x00080100,
			0x00080000, 0x42000100, 0x00000100, 0x02000000,
			0x40000000, 0x02080000, 0x42000100, 0x40080100,
			0x02000100, 0x40000000, 0x42080000, 0x02080100,
			0x40080100, 0x00000100, 0x02000000, 0x42080000,
			0x42080100, 0x00080100, 0x42000000, 0x42080100,
			0x02080000, 0x00000000, 0x40080000, 0x42000000,
			0x00080100, 0x02000100, 0x40000100, 0x00080000,
			0x00000000, 0x40080000, 0x02080100, 0x40000100
			);

		$SP6 = array(
			0x20000010, 0x20400000, 0x00004000, 0x20404010,
			0x20400000, 0x00000010, 0x20404010, 0x00400000,
			0x20004000, 0x00404010, 0x00400000, 0x20000010,
			0x00400010, 0x20004000, 0x20000000, 0x00004010,
			0x00000000, 0x00400010, 0x20004010, 0x00004000,
			0x00404000, 0x20004010, 0x00000010, 0x20400010,
			0x20400010, 0x00000000, 0x00404010, 0x20404000,
			0x00004010, 0x00404000, 0x20404000, 0x20000000,
			0x20004000, 0x00000010, 0x20400010, 0x00404000,
			0x20404010, 0x00400000, 0x00004010, 0x20000010,
			0x00400000, 0x20004000, 0x20000000, 0x00004010,
			0x20000010, 0x20404010, 0x00404000, 0x20400000,
			0x00404010, 0x20404000, 0x00000000, 0x20400010,
			0x00000010, 0x00004000, 0x20400000, 0x00404010,
			0x00004000, 0x00400010, 0x20004010, 0x00000000,
			0x20404000, 0x20000000, 0x00400010, 0x20004010
			);

		$SP7 = array( 
			0x00200000, 0x04200002, 0x04000802, 0x00000000,
			0x00000800, 0x04000802, 0x00200802, 0x04200800,
			0x04200802, 0x00200000, 0x00000000, 0x04000002,
			0x00000002, 0x04000000, 0x04200002, 0x00000802,
			0x04000800, 0x00200802, 0x00200002, 0x04000800,
			0x04000002, 0x04200000, 0x04200800, 0x00200002,
			0x04200000, 0x00000800, 0x00000802, 0x04200802,
			0x00200800, 0x00000002, 0x04000000, 0x00200800,
			0x04000000, 0x00200800, 0x00200000, 0x04000802,
			0x04000802, 0x04200002, 0x04200002, 0x00000002,
			0x00200002, 0x04000000, 0x04000800, 0x00200000,
			0x04200800, 0x00000802, 0x00200802, 0x04200800,
			0x00000802, 0x04000002, 0x04200802, 0x04200000,
			0x00200800, 0x00000000, 0x00000002, 0x04200802,
			0x00000000, 0x00200802, 0x04200000, 0x00000800,
			0x04000002, 0x04000800, 0x00000800, 0x00200002
			);

		$SP8 = array(
			0x10001040, 0x00001000, 0x00040000, 0x10041040,
			0x10000000, 0x10001040, 0x00000040, 0x10000000,
			0x00040040, 0x10040000, 0x10041040, 0x00041000,
			0x10041000, 0x00041040, 0x00001000, 0x00000040,
			0x10040000, 0x10000040, 0x10001000, 0x00001040,
			0x00041000, 0x00040040, 0x10040040, 0x10041000,
			0x00001040, 0x00000000, 0x00000000, 0x10040040,
			0x10000040, 0x10001000, 0x00041040, 0x00040000,
			0x00041040, 0x00040000, 0x10041000, 0x00001000,
			0x00000040, 0x10040040, 0x00001000, 0x00041040,
			0x10001000, 0x00000040, 0x10000040, 0x10040000,
			0x10040040, 0x10000000, 0x00040000, 0x10001040,
			0x00000000, 0x10041040, 0x00040040, 0x10000040,
			0x10040000, 0x10001000, 0x10001040, 0x00000000,
			0x10041040, 0x00041000, 0x00041000, 0x00001040,
			0x00001040, 0x00040040, 0x10000000, 0x10041000
			);


		/*
			void deskey(key, edf)	// Thanks to James Gillogly & Phil Karn!
			unsigned char *key;
			int edf;
		*/

	  	$pc1m = array_pad(array(),56,0);
	  	$pcr = array_pad(array(),56,0);
		$kn = array_pad(array(),32,0);
	  
		for ($j =0; $j < 56; $j++)
		{
			$l = $pc1[$j];
			$m = $l & 07;

			if ($key[$l >> 3] & $bytebit[$m])   // safe rshift, small int
			{
				$pc1m[$j] = 1;
			}
			else
			{
				$pc1m[$j] = 0;
			}
		}

		for($i = 0; $i < 16; $i++)
		{
			if ($edf)
			{
				$m = (15 - $i) << 1;
			}
			else
			{
				$m = $i << 1;
			}

			$n = $m + 1;

			$kn[$m] = $kn[$n] = 0;

			for($j = 0; $j < 28; $j++)
			{
				$l = $j + $totrot[$i];

				if ($l < 28)
				{
					$pcr[$j] = $pc1m[$l];
				}
				else
				{
					$pcr[$j] = $pc1m[$l - 28];
				}
			}

			for($j = 28; $j < 56; $j++)
			{
				$l = $j + $totrot[$i];

				if ($l < 56)
				{
					$pcr[$j] = $pc1m[$l];
				}
				else
				{
					$pcr[$j] = $pc1m[$l - 28];
				}
			}

			for($j = 0; $j < 24; $j++)
			{
				if ($pcr[$pc2[$j]])
				{
					$kn[$m] |= $bigbyte[$j];
				}

				if ($pcr[$pc2[$j+24]])
				{
					$kn[$n] |= $bigbyte[$j];
				}
			}
		}

		/*
			static void cookey(raw1)
			register unsigned long *raw1;

			void usekey(from)
			register unsigned long *from;
		*/

		$keys = array();

		for($i = 0; $i < 32; $i = $i + 2)
		{
			$raw0 = $kn[$i];
			$raw1 = $kn[$i+1];

			$k  = ($raw0 & 0x00fc0000) << 6;
			$k |= ($raw0 & 0x00000fc0) << 10;
			$k |= ($raw1 & 0x00fc0000) >> 10; // safe rshift, sign bit always 0
			$k |= ($raw1 & 0x00000fc0) >> 6;  // safe rshift, sign bit always 0

			$keys[] = $k;

			$k  = ($raw0 & 0x0003f000) << 12;
			$k |= ($raw0 & 0x0000003f) << 16;
			$k |= ($raw1 & 0x0003f000) >> 4; // safe rshift, sign bit always 0
			$k |= ($raw1 & 0x0000003f);

			$keys[] = $k;
		}

		/*
			static void desfunc(block, keys)
			register unsigned long *block, *keys;
		*/

		$leftt = $block[0]<<24|$block[1]<<16|$block[2]<<8|$block[3];
		$right = $block[4]<<24|$block[5]<<16|$block[6]<<8|$block[7];
		$work = (($leftt >> 4) ^ $right) & 0x0f0f0f0f; // safe rshift, top 4 bits not used

		$right ^= $work;
		$leftt ^= ($work << 4);
		$work = (($leftt >> 16) ^ $right) & 0x0000ffff; // safe rshift, top 16 bits not used

		$right ^= $work;
		$leftt ^= ($work << 16);
		$work = (($right >> 2) ^ $leftt) & 0x33333333; // safe rshift, top 2 bits not used

		$leftt ^= $work;
		$right ^= ($work << 2);
		$work = (($right >> 8) ^ $leftt) & 0x00ff00ff; // safe rshift, top 8 bits not used

		$leftt ^= $work;
		$right ^= ($work << 8);
		$right = (($right << 1) | (($right >> 31) & 1)) & 0xffffffff; // safe rshift, top 31 bits masked out
		$work = ($leftt ^ $right) & 0xaaaaaaaa;

		$leftt ^= $work;
		$right ^= $work;
		$leftt = (($leftt << 1) | (($leftt >> 31) & 1)) & 0xffffffff; // safe rshift, top 31 bits masked out

		for($i = 0; $i < 32; $i = $i + 4)
		{
			$work  = ($right << 28) | (($right >> 4)&0x0fffffff); // possibly UNSAFE rshift

			$work ^= $keys[$i];
			$fval  = $SP7[ $work		 & 0x3f];
			$fval |= $SP5[($work >>  8) & 0x3f]; // safe rshift, top 8 bits not used
			$fval |= $SP3[($work >> 16) & 0x3f]; // safe rshift, top 16 bits not used
			$fval |= $SP1[($work >> 24) & 0x3f]; // safe rshift, top 24 bits not used

			$work  = $right ^ $keys[$i+1];
			$fval |= $SP8[ $work		 & 0x3f];
			$fval |= $SP6[($work >>  8) & 0x3f]; // safe rshift, top 8 bits not used
			$fval |= $SP4[($work >> 16) & 0x3f]; // safe rshift, top 16 bits not used
			$fval |= $SP2[($work >> 24) & 0x3f]; // safe rshift, top 24 bits not used
			$leftt ^= $fval;

			$work  = ($leftt << 28) | (($leftt >> 4)&0x0fffffff); // possibly UNSAFE rshift
			$work ^= $keys[$i+2];
			$fval  = $SP7[ $work		 & 0x3f];
			$fval |= $SP5[($work >>  8) & 0x3f]; // safe rshift, top 8 bits not used
			$fval |= $SP3[($work >> 16) & 0x3f]; // safe rshift, top 16 bits not used
			$fval |= $SP1[($work >> 24) & 0x3f]; // safe rshift, top 24 bits not used

			$work  = $leftt ^ $keys[$i+3];
			$fval |= $SP8[ $work		 & 0x3f];
			$fval |= $SP6[($work >>  8) & 0x3f]; // safe rshift, top 8 bits not used
			$fval |= $SP4[($work >> 16) & 0x3f]; // safe rshift, top 16 bits not used
			$fval |= $SP2[($work >> 24) & 0x3f]; // safe rshift, top 24 bits not used

			$right ^= $fval;
		}

		$right = ($right << 31) | (($right >> 1)&0x7fffffff); // possibly UNSAFE rshift
		$work = ($leftt ^ $right) & 0xaaaaaaaa;

		$leftt ^= $work;
		$right ^= $work;
		$leftt = ($leftt << 31) | (($leftt >> 1)&0x7fffffff); // possibly UNSAFE rshift
		$work = (($leftt >> 8) ^ $right) & 0x00ff00ff; // safe rshift, top 8 bits not used

		$right ^= $work;
		$leftt ^= ($work << 8);
		$work = (($leftt >> 2) ^ $right) & 0x33333333; // safe rshift, top 2 bits not used

		$right ^= $work;
		$leftt ^= ($work << 2);
		$work = (($right >> 16) ^ $leftt) & 0x0000ffff; // safe rshift, top 16 bits not used

		$leftt ^= $work;
		$right ^= ($work << 16);
		$work = (($right >> 4) ^ $leftt) & 0x0f0f0f0f; // safe rshift, top 4 bits not used

		$leftt ^= $work;
		$right ^= ($work << 4);

		$leftt &= 0xffffffff;
		$right &= 0xffffffff;

		return array( 
		    ($right>>24)&0xFF, // safe rshift, top 24 bits not used
		    ($right>>16)&0xFF, // safe rshift, top 16 bits not used
		    ($right>>8)&0xFF,  // safe rshift, top 8 bits not used
		    $right&0xFF,
		    ($leftt >> 24)&0xFF, // safe rshift, top 24 bits not used
		    ($leftt >> 16)&0xFF, // safe rshift, top 16 bits not used
		    ($leftt >> 8)&0xFF,  // safe rshift, top 8 bits not used
		    $leftt & 0xFF
		    );
	}

	/* Validation sets:
 	*
 	* Single-length key, single-length plaintext -
 	* Key	  : 0123 4567 89ab cdef
 	* Plain  : 0123 4567 89ab cde7
 	* Cipher : c957 4425 6a5e d31d
 	*
 	* d3des V5.0a rwo 9208.07 18:44 Graven Imagery
	 **********************************************************************/
}

function genrandpassword()
{
	$len = mt_rand(0,8);
	$password = ""; // "\0\0\0\0\0\0\0\0";
	for($i=0; $i<$len; $i++)
	{
		$c = mt_rand(32,255);

		if ($c == ord("'"))
			$c = ord('a');

		$password .= chr($c);

	}
	return $password;
}
