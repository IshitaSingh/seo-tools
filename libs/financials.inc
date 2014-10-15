<?php
/*******************************************************************************
 *	Authors		:	$Authors: lhb, tomander $
 *
 *	Copyright	:	phpFinancials authors
 *
 *******************************************************************************
*/

class financials {

// FUNCTIONS ABOUT TVM (TIME VALUE OF MONEY)
// NPER, PMT, PV, FV (ABOUT RATE: SEE FURTHER ON)

	/**
	 * Payment based on constant payments and rate
	 *
	 * The payment function calculates a periodic payment for a loan 
	 * based on constant payments and a constant interest rate.
	 *
	 * @param float $rate interest rate per period
 	 * @param float $nper total number of periods (payments)
 	 * @param float $pv present value 
 	 * @param float $fv future value
 	 * @param integer $type type of payment (0 at end of period, 1 at beginning)
	 * @return float
	 * @access public
	 * @authors lhb, tomander
	 *
	 **/

     // author lhb 
                                                          
      function NPER($rate, $pmt, $pv, $fv, $type) {
		
		$hold = 0.0;
		
		if ($rate <= 0.0) {
			trigger_error("Return by zero");
			return FALSE;
		}
		
		$hold = ($pmt * (1.0 + $rate * $type) - $fv * $rate) / ($pv * $rate + $pmt * (1.0 + $rate * $type));

		if ($hold <= 0.0) {
			trigger_error("Couldn't calc NPER");
			return;
		} else {
			return (float) (log ($hold) / log (1.0 + $rate));
		}
	} // end function NPER

        // author lhb
	function PMT($rate, $nper, $pv, $fv = 0, $type) {
	
		//$pvif = self::PVIF($rate, $nper);
		$fvif = self::FVIF($rate, $nper); 
		$fvifa = self::FVIFA($rate, $nper);

        //return ((-$pv * $pvif - $fv ) / ((1.0 + $rate * $type) * $fvifa));
        return ((-$pv * $fvif - $fv ) / ((1.0 + $rate * $type) * $fvifa)); 
        
	} // end function PMT
	

       /**
	 * Present Value (PV)
	 *
	 * The Present Value (PV) function calculates the present value of an 
	 * investment. It returns the total value of a series of payments at the
	 * current time.
	 *
 	 * @param float $rate interest rate
 	 * @param float $nper total number of periods
 	 * @param float $pmt payment for each period
 	 * @param float $fv future value
 	 * @param integer $type type of payment (0 at end of period, 1 at beginning)
	 * @return float
	 * @access public
	 * @author lhb
	 *
	 **/	
	
	                                                       
	function PV($rate, $nper, $pmt, $fv = 0, $type) {
	
		$fvif = 0.0; // changed from pvif to fvif by tomander because definition of fvif and pvif changed as above
		$fvifa = 0.0; 
	
		// if can't calculate pvif or fvifa, return the negative of number of
		// payments multiplied by the payment

		if ($rate == 0) return (float) (-$nper * $pmt);

		$fvif = self::FVIF($rate, $nper); // changed from pvif to fvif by tomander because definition of fvif and pvif changed as above
		$fvifa = self::FVIFA($rate, $nper);
		
		// make sure we won't div0
		if ($fvif == 0) {
			trigger_error("Division by zero.");
			return FALSE;
		} else {
	        return (float) ((-$fv - $pmt * (1.0 + $rate * $type) * $fvifa) / $fvif); 
	        // changed from pvif to fvif by tomander because definition of fvif and pvif changed as above
		}
		
	} //end of function PV


        /**
	 * Future Value (FV)
	 *
	 * The Future Value (FV) function calculates the future value of an 
	 * investment based on a series of constant periodic payments and a fixed
	 * interest rate. It returns the total value of a series of payments at the
	 * end of the periods.
	 *
 	 * @param float $rate interest rate
 	 * @param float $nper total number of periods
 	 * @param float $pmt payment for each period
 	 * @param float $pv present value
 	 * @param integer $type type of payment (0 at end of period, 1 at beginning)
	 * @return float
	 * @access public
	 * @author lhb
	 *
	 **/
	 
	function FV($rate, $nper, $pmt, $pv, $type) {
	
		$fvif = self::FVIF($rate, $nper); 
		// changed from pvif to fvif by tomander because definition of fvif and pvif changed as above
		$fvifa = self::FVIFA($rate, $nper);
		
		return (float) (-(($pv * $fvif) + $pmt * (1.0 + $rate * $type) * $fvifa));
		// changed from pvif to fvif by tomander because definition of fvif and pvif changed as above
		
	} // end function FV

	
	
	/**	 
// FUNCTIONS ABOUT PRESENT VALUE AND FUTURE VALUE OF SINGLE PAYMENT AND SERIES OF EQUAL PAYMENTS
// PVIF, FVIF, PVIFA, FVIFA
	 
	 * ( NOT Present Value Interest Factor (PVIF) )
	 * Future Value Interest Factor (FVIF) (Note by tomander)
	 *
 	 * @param float $rate interest rate
 	 * @param float $nper total number of periods
	 * @return float
	 * @access public
	 * @author lhb
	 **/

	function FVIF($rate, $nper) {
		return (pow(1 + $rate, $nper));
	}

	
	/**
	 * (NOT Future Value Interest Factor (FVIF) )
	 * Present Value Interest Factor (PVIF) (Note by tomander)
	 *
 	 * @param float $rate interest rate
 	 * @param float $nper total number of periods
	 * @return float
	 * @access public
	 * @author lhb
	 **/

	function PVIF($rate, $nper) {
		return (1.0 / self::FVIF($rate, $nper));
	}
	

	/**
	 * Present Value Interest Factor of Annuities (PVIFA).
	 * Note by tomander:
	 * PVIFA (called USPV in HP calculator manual) calculates the present value of a series of eaual payments done nper times.
	 *
	 * @param float $rate interest rate
 	 * @param float $nper number of periods
	 * @return float
	 * @access public
	 * @author lhb
	 *
	 **/
	 
	function PVIFA($rate, $nper) {
		return ((1.0 / $rate) - (1.0 / ($rate * pow (1 + $rate, $nper))));
	}


	/**
	 * Future Value Interest Factor of Annuities (FVIFA).
	 * Note by tomander:
	 * FVIFA (called USFV in HP calculator manual) calculates the future value of a series of equal payments done nper times.
	 * 
	 * phpfinancials::FVIFA()
 	 * @param float $rate interest rate
 	 * @param float $nper number of periods
	 * @return float
	 * @access public
	 * @author lhb 
	 **/
	 
	function FVIFA($rate, $nper) {
		return ((pow (1 + $rate, $nper) - 1) / $rate);
	}
	

// FUNCTIONS ABOUT CASH FLOW
// NPV, (NFV, NUS, IRR)
	
	/**
	 * Net Present Value (NPV)
	 *
	 * Calculate the Net Present Value (NPV) of an investment based on a 
	 * discount rate, a series of periodic payments (negative) and periodic 
	 * income (positive). 
	 *
	 * @param float $rate interest rate
 	 * @param array $values period payments
	 * @return float
	 * @access public
	 * @author lhb
	 *
	 **/	

	function NPV($rate, $values = array()) { 

		$npv = 0.0;
		$its = count($values) -1;	// iterations for array values
		
		for ($i=0;$i<=$its;$i+=1) { 
			$npv = $values[$its - $i] + $npv / (1 + $rate); 
		} 
		
		return $npv;
		
	}
	
        
// NFV not used yet. Not tested.   
        /**
	 * Net Future Value (NFV)
	 *
	 * Calculate the Net Future Value (NPV) of an investment based on a 
	 * discount rate, a series of periodic payments (negative) and periodic 
	 * income (positive). In other words NFV is future value of NPV.
	 *
	 * @param float $rate interest rate
 	 * @param array $values period payments
	 * @return float
	 * @access public
	 * @author tomander
	 *
	 **/		

/*
	function NFV($rate, $values = array()) { 

		$nfv = 0.0;
		$its = count($values) -1;	// iterations for array values
		
		for ($i=0;$i<=$its;$i+=1) { 
			$nfv = $npv * pow(1 + $rate,$nper)
		} 
		
		return $nfv;
		
	}

*/

// FUNCTIONS ABOUT INTEREST RATES 
// EFFECT, APR, RATE

	/**
	 * Effective interest from nominal rate (EFFECT)
	 *
	 * Calculate the effective interest (EFFECT) from a nominal rate.
	 *
 	 * @param float $rate interest rate
 	 * @param array $nper number of periods
	 * @return float
	 * @access public
	 * @author lhb
	 *
	 **/
	
	function EFFECT($rate, $nper) {
	
		if ($rate < 0 || $nper <= 0) {
			trigger_error("Nominal interest rate must be > 0 and no. of periods must be > 0");
			return FALSE;
		}
			
		return (float) (pow ((1 + $rate*0.01 / $nper), $nper) - 1);
		
	} // end function EFFECT
	
	
	/**function APR($nper, $rate)
	
	 * This function is equal to EFFECT but called APR
	 * APR = Annual Percentage Rate = Effective Interest Rate per year
	 *
	 * Calculate the APR given the periodic interest rate (RATE)
	 *
 	 * @param $nper number of compounding periods
 	 * @param $rate nominal interest rate per year
	 * @return float 
	 * @access public
	 * @author tomander
	 *
	 **/
	
	function APR($nper, $rate) {
	
		if ($nper <= 0) {
			trigger_error("compunding periods must be > 0");
			return FALSE;
		}
			
		return (float) (pow ( 1 + (($rate*0.01)/$nper) , $nper) - 1);
		
	} // end function APR
	

        /**
	 * RATE = Periodic Interest Rate 
	 *
	 * Calculate periodic interest rate (RATE) out of $apr and $nper
	 *
 	 * @param $nper number of compounding periods
 	 * @param $apr annual percentage rate or effective interest rate
	 * @return float 
	 * @access public
	 * @author tomander
	 *
	 **/

	function RATE($nper, $apr) {
	
		if ($nper <= 0) {
			trigger_error("compunding periods must be > 0");
			return FALSE;
		}
			
		return (float) (pow( 1 + ($apr*0.01) , 1/$nper) - 1) ;
		
	} // end function RATE

        
         /**
	 * NOM = Nominal Interest Rate
	 * This is the same formula as RATE multiplied by $nper
	 * Calculate nominal interest rate (NOM) out of $apr and $nper
	 *
 	 * @param $nper number of compounding periods
 	 * @param $apr annual percentage rate or effective interest rate
	 * @return float 
	 * @access public
	 * @author tomander
	 *
	 **/

	function NOM($nper, $apr) {
	
		if ($nper <= 0) {
			trigger_error("compunding periods must be > 0");
			return FALSE;
		}
			
		return (float) ( $nper * (pow( 1 + ($apr*0.01) , 1/$nper) - 1) ) ;
		
	} // end function NOM


	

// FUNCTIONS ABOUT DEPRECIATION
// DB, DDB, SLN, SYD

        // Compared to use of HP-calculator:
        // No particular function for function DB in a HP-calculator.
        // Function closest to it is SYD, which is called SOYD in HP-calculator

	/**
	 * Depreciation using fixed declining balanced method.
	 *
	 * Return the depreciation at $period using fixed declining balanced
	 * method.
	 *
 	 * @param float $cost investment cost
 	 * @param float $salvage what is it worth at the end of its life
 	 * @param float $life lifetime (how long does it take to write it off)
 	 * @param float $period at which period (same scale as life)
 	 * @param integer $month number of months in the first year
	 * @return float
	 * @access public
	 * @author lhb
	 *
	 **/
	
	function DB($cost, $salvage, $life, $period, $month = 12) {

		$rate = 0.0;
		$total = 0.0;
		$i = 0;

		if ($cost == 0 || $life <= 0 || $salvage / $cost < 0) {
			trigger_error("Bad input in DB");
			return FALSE;	
		}

		$rate = 1 - pow(($salvage / $cost), (1 / $life));
		$rate *= 1000;
		$rate = floor($rate+0.5) / 1000;

		$total = $cost * $rate * $month / 12;

		if ($period == 1) return (float) ($total);
		
		for ($i=1;$i<$life;$i++) {

			if ($i == $period - 1) {
				return (float) ($cost - $total) * $rate;
			} else {
				$total += ($cost - $total) * $rate;
			}
		}
		
		return (float) ((($cost - $total) * $rate * (12 - $month)) / 12);

	} // end function DB

	/**
	 * Depreciation using variable factor
	 *
	 * Return the depreciation at $period using double declining balance method
	 * or a method specified in $factor.
	 *
 	 * @param float $cost investment cost
 	 * @param float $salvage what is it worth at the end of its life
 	 * @param float $life lifetime (how long does it take to write it off)
 	 * @param float $period at which period (same scale as life)
 	 * @param integer $factor with which to depreciate
	 * @return float
	 * @access public
	 * @author lhb
	 *
	 **/
	
	function DDB($cost, $salvage, $life, $period, $factor = 2) {

		$total = 0.0;
		$i = 0;
		$dep = 0.0;

		if ($life <= 0) {
			trigger_error("Bad input in DDB, need more life");
			return FALSE;
		}

		for ($i=0;$i<$life-1;$i++) {
		
			$dep = ($cost - $total) * ($factor / $life);
			
			if ($period - 1 == $i) {
				return (float) ($dep);
			} else {
				$total += $dep;
			}
		}

		return (float) ($cost - $total - $salvage);

	} // end function DDB

	
	/**
	 * Straight Line depreciation of investment
	 *
	 * Calculate the straight line depreciation for an investment for one term.
	 *
 	 * @param float $cost investment cost
 	 * @param float $salvage what is it worth at the end of its life
 	 * @param float $life lifetime
	 * @return float
	 * @access public
	 * @author lhb
	 *
	 **/
	
	function SLN($cost, $salvage, $life) {

		if ($life <= 0) {
			trigger_error("Bad input in SLN, need more life");
			return FALSE;
		}

		return (float) (($cost - $salvage) / $life);

	} // end function SLN

	
	/**
	 * Sum of Years Digit method.
	 *
	 * Calculate the depreciation of an investment for $term using the 
	 * Sum of Years Digit method.
	 *
 	 * @param float $cost investment cost
 	 * @param float $salvage what is it worth at the end of its life
 	 * @param float $life lifetime
 	 * @param float $period at what period
	 * @return float
	 * @access public
	 * @author lhb
	 * 
	 **/


       // Compared to use od HP-calculator:
       // function SYD called SOYD in HP-calculator
	
	function SYD($cost, $salvage, $life, $period) {

		if ($life <= 0) {
			trigger_error("Bad input in SYD, need more life");
			return FALSE;
		}

		return (float) ((($cost - $salvage) * ($life - $period + 1) * 2) / ($life * ($life + 1.0)));
	
	} // end function SYD


	/**
	 * Number of terms for an investment
	 *
	 * Calculate the number of terms of an investment based on a periodic
	 * payment and a constant interest rate
	 *
 	 * @param float $rate interest rate
 	 * @param float $pmt done each period
 	 * @param float $pv present value
 	 * @param float $fv future value
 	 * @param integer $type type of payment (0 at end of period, 1 at beginning)
	 * @return float
	 * @access public
	 * @author lhb
	 *
	 **/
	
// OTHER
	
	// not working
	function GIR($values, $frate, $hrate) {
		
		$nvalues = array();

		while(list($key, $val) = each($values)) {
			//$values[$key] = abs($val);
			$nvalues[$key] = -$val;
		}
		
		$a = -self::NPV($hrate, $values) * pow((1.0 + $hrate), count($values));
		$b = self::NPV($frate, $nvalues) * (1.0 + $frate);
		$c = pow(($a / $b), (1 / (count($values) -1)));

		return $c;
	}

// PERCENT CALCULATIONS

	// author tomander
	// calculates the difference in percent between two values
	
	function CHG($old, $new) {
	
		$chg = ( ($new-$old)/$old ) * 100;
		
		return $chg;
		
		} // end of function chg
		



	
} // end class phpfinancials
?>
