<?php

class Graph {
	public $slide = 1;
	public $period = null;
	public $background = 'FFFFFF';
	public $lables = '7F7F7F';
	public $foreground = '000000';
	public $width = null;
	public $height = null;
	public $vertical_lable = false;
	public $horizontal_lable = false;

	private $data = array();
	private $max = 0;
	private $max_index = 0;

	public function add($value, $index, $label) {
		$this->data[$label][$index] = $value;
		$this->max = max($this->max, $value);
		$this->max_index = max($this->max_index, $index);
	}

	public function draw() {
		if(!isset($this->period)) {
			$this->period = $this->max_index;
		}
		if(!isset($this->height)) {
			$this->height = $this->max;
		}
		if(!isset($this->width)) {
			$this->width = $this->period;
		}
		if(!isset($this->img)) {
			$this->img = imagecreate($this->width, $this->height);
		}
		$this->background = ImageColorAllocate($this->img,
			'0x'.substr($this->background, 0, 2),
			'0x'.substr($this->background, 2, 2),
			'0x'.substr($this->background, 4, 2)
		);
		$this->lables = ImageColorAllocate($this->img,
			'0x'.substr($this->lables, 0, 2),
			'0x'.substr($this->lables, 2, 2),
			'0x'.substr($this->lables, 4, 2)
		);
		$this->foreground = ImageColorAllocate($this->img,
			'0x'.substr($this->foreground, 0, 2),
			'0x'.substr($this->foreground, 2, 2),
			'0x'.substr($this->foreground, 4, 2)
		);
		if($this->vertical_lable && !empty($this->data)) {
			$step = pow(2, round(log($this->max/$this->vertical_lable, 2)));
			$substep = pow(10, round(log($step, 10))-1);
			for($i=1, $s=round($step/$substep)*$substep; $s <= $this->max; $s=round($step*(++$i)/$substep)*$substep) {
				$h = $s*$this->height/$this->max;
				imageline(
					$this->img,
					0,
					$this->height-$h,
					$this->width,
					$this->height-$h,
					$this->lables
				);
				imagestring($this->img, 1, 1, $this->height-$h+1, $s, $this->lables);
			}
		}
		foreach($this->data as $label => $data) {
			$old_height = 0;
			for($i=0; $i < $this->period; $i++) {
				$value = 0;
				$k = 0;
				for($j=floor($i-($this->slide/2)); $j<floor(($i*2+$this->slide)/2); $j++) {
					if(isset($data[$j])) {
						$value += $data[$j];
					}
				}
				$value = $value*$this->height/$this->max/$this->slide;
				imageline(
					$this->img,
					($i-1) * $this->width / $this->period,
					$this->height - $old_height-1,
					$i * $this->width / $this->period,
					$this->height - $value-1,
					$this->foreground
				);
				$old_height = $value;
			}
		}
		header("Content-type: image/png");
		if(!imagepng($this->img)){
			die('hej');
		}
	}
}
?>
