<?php
/**
 * @title Matrix
 *
 *  @abstract Utility class for manipulating matrices in {@link CoordinateSpace}
 *
 * @package lib.util
 *
 */
class Matrix {

  public function __construct() {
    $this->a = array_fill(0,4,array_fill(0,4,0.0));
    $this->a[0][0] = $this->a[1][1] = $this->a[2][2] = $this->a[3][3] = 1.0;
  }


  public static function transform(Matrix $m, $u) {
    $u[3] = 1.0;
    $v = array_fill(0,4,0.0);
    $v[3] = 1.0;
    for ($i = 0; $i < 4; ++$i)
    for ($j = 0; $j < 4; ++$j)
    $v[$i] += $m->a[$i][$j] * $u[$j];
    return $v;
  }

  public static function multiply(Matrix $l, Matrix $r) {
    $m = new Matrix();
    for ($i = 0; $i < 4; ++$i)
    for ($j = 0; $j < 4; ++$j) {
      $c = 0.0;
      for ($k = 0; $k < 4; ++$k)
      $c += $l->a[$i][$k] * $r->a[$k][$j];
      $m->a[$i][$j] = $c;
    }
    return $m;
  }

  public static function add(Matrix $l, Matrix $r) {
    $m = new Matrix();

    for ($i = 0; $i < 4; ++$i)
    for ($j = 0; $j < 4; ++$j)
    $m->a[$i][$j] = $l->a[$i][$j] + $r->a[$i][$j];

    return $m;
  }


  public static function rotateZ($phi) {
    $m = new Matrix();
    $m->a[0][0] = cos($phi); $m->a[0][1] = -sin($phi); $m->a[0][2] = 0.0; $m->a[0][3] = 0.0;
    $m->a[1][0] = sin($phi); $m->a[1][1] =  cos($phi); $m->a[1][2] = 0.0; $m->a[1][3] = 0.0;
    $m->a[2][0] = 0.0      ; $m->a[2][1] =  0.0      ; $m->a[2][2] = 1.0; $m->a[2][3] = 0.0;
    $m->a[3][0] = 0.0      ; $m->a[3][1] =  0.0      ; $m->a[3][2] = 0.0; $m->a[3][3] = 1.0;
    return $m;
  }

  public static function rotateX($theta) {
    $m = new Matrix();
    $m->a[0][0] = 1.0 ; $m->a[0][1] =  0.0        ; $m->a[0][2] = 0.0         ; $m->a[0][3] = 0.0;
    $m->a[1][0] = 0.0 ; $m->a[1][1] =  cos($theta); $m->a[1][2] = -sin($theta); $m->a[1][3] = 0.0;
    $m->a[2][0] = 0.0 ; $m->a[2][1] =  sin($theta); $m->a[2][2] =  cos($theta); $m->a[2][3] = 0.0;
    $m->a[3][0] = 0.0 ; $m->a[3][1] =  0.0        ; $m->a[3][2] = 0.0         ; $m->a[3][3] = 1.0;
    return $m;
  }

  public static function rotateY($alpha) {
    $m = new Matrix();
    $m->a[0][0] =  cos($alpha) ; $m->a[0][1] =  0.0; $m->a[0][2] = sin($alpha) ; $m->a[0][3] = 0.0;
    $m->a[1][0] =  0.0         ; $m->a[1][1] =  1.0; $m->a[1][2] = 0.0         ; $m->a[1][3] = 0.0;
    $m->a[2][0] = -sin($alpha) ; $m->a[2][1] =  0.0; $m->a[2][2] = cos($alpha);  $m->a[2][3] = 0.0;
    $m->a[3][0] =  0.0         ; $m->a[3][1] =  0.0; $m->a[3][2] = 0.0         ; $m->a[3][3] = 1.0;
    return $m;
  }

  public static function rotate($phi, $theta, $psi) {
    $m0 = Matrix::rotateZ($phi);
    $m1 = Matrix::rotateX($theta);
    $m2 = Matrix::rotateZ($psi);
    return Matrix::multiply($m2, Matrix::multiply($m1, $m0));
  }

  public static function translate($dx,$dy,$dz) {
    $m = new Matrix();
    $m->a[0][3] = $dx;
    $m->a[1][3] = $dy;
    $m->a[2][3] = $dz;
    $m->a[3][3] = 1.0;
    return $m;
  }

  public static function scale($x,$y,$z) {
    $m = new Matrix();
    $m->a[0][0] = $x;
    $m->a[1][1] = $y;
    $m->a[2][2] = $z;
    $m->a[3][3] = 1.0;
    return $m;
  }



  public function rot($phi, $theta, $psi) {
    $this->a = Matrix::multiply(Matrix::rotate($phi,$theta,$psi),$this)->a;
    return $this;
  }

  public function scl($x,$y,$z) {
    $this->a = Matrix::multiply(Matrix::scale($x,$y,$z),$this)->a;
    return $this;
  }

  public function trn($x,$y,$z) {
    $this->a = Matrix::multiply(Matrix::translate($x,$y,$z),$this)->a;
    return $this;
  }

  public function apply($x,$y,$z) {
    return Matrix::transform($this,array($x,$y,$z,1.0));
  }


  public function __toString() {
    $r = "";
    for ($i = 0; $i<4; ++$i)
    $r .= sprintf("|%4.4f\t%4.4f\t%4.4f\t%4.4f|\n", $this->a[$i][0],$this->a[$i][1],$this->a[$i][2],$this->a[$i][3]);
    return $r;
  }
} // Matrix

?>