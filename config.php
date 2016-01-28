<?php
// Parameters
$dnsiStr = 'Δ/νση Π.Ε. Ηρακλείου';
$dnsiStrShort = 'ΔΙ.Π.Ε. Ηρακλείου';
$dnsiLink = 'http://dipe.ira.sch.gr/site';
// set canLog to 1 to enable logging to file
// file must be .txt to be protected from access
$canLog = 0;
$logFile = 'login_log.txt';
// clean up pdf files after XX minutes
$cleanUpAfter = 10;

// Espa column headers
// ESPA csv is a mess, with duplicate column names, that's why we keep field numbers in this array...
$hdr = Array (
   'Α/Α' => 0, 'Ονοματεπώνυμο' => 1,'ΕΙΔΟΣ ΑΠΑΣΧ.' => 2, 'ΥΠΟΧ. ΩΡΑΡ.' => 3, 'ΔΕ' => 4, 'ΩΡΕΣ ΑΝΑ ΕΒΔ.' => 5, 'ΗΜΕΡΕΣ' => 6, 'ΚΩΔΙΚ.' => 7, 'ΑΦΜ' => 8, 'Μ.Κ.(BM)' => 9,
   'ΕΙΔ. ΑΠ.' => 10, 'ΗΜ.ΑΣΦ.' => 11, 'Μ.Κ.(ΠΟΣΟ)' => 12, 'Ο.Ε.' => 13, 'ΕΠΠ' => 14, 'ΣΥΝΟΛΟ(ΑΠ)' => 15, 'ΦΟΡΟΣ' => 16, 'ΕΚΤΑΚΤΗ ΕΙΣΦΟΡΑ' => 17,
   'ΟΑΕΔ' => 18, 'ΙΚΑ' => 19, 'ΕΡΓΑΖΟΜ.' => 20, 'ΕΡΓΟΔΟΤΗ' => 21, 'ΣΥΝΟΛΟ(ΙΚΑ)' => 22, 'ΤΑΜΕΙΟ' => 23, 'ΕΡΓΑΖΟΜ.(ΤΑΜ)' => 24, 'ΕΡΓΟΔΟΤΗ(ΤΑΜ)' => 25, 'ΣΥΝΟΛΟ(ΤΑΜ)' => 26,
   'ΑΧΡΕΩΣΤ.' => 27, 'ΑΦ.ΠΟΣΟ' => 28, 'ΕΠΙΔ.ΕΡΓ.' => 29, 'ΑΠΟΖ.ΙΚΑ' => 30, 'ΚΑΘΑΡΑ' => 31, 'ΜΕ ΑΠ.ΤΑΜ.' => 32, 'ΜΕ ΑΠ.ΤΑΜ. 2' => 33, 'ΕΝΑΝΤΙ' => 34, 'ΔΙΑΦΟΡΑ' => 35
);

 ?>
