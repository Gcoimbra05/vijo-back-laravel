<?php

namespace App\Services\Emlo;

use App\Services\Emlo\EmloDatabaseLoader;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class EmloCsvService
{
    public function createCsvFile($emotionData)
    {
        $csvHeaders = $this->getCsvHeaders();

        if ($this->validateEmotionsInput($emotionData)){       
            
            $rowData = $this->calculateRowsAndAppendToCSV($emotionData, $csvHeaders);
            $csvHeadersWRows = $rowData['csvHeadersWRows'];
            $this->calculateAveragesAndAppendToCSV($rowData['allRowsData'], $csvHeadersWRows);

            // Create CSV file
            $csv_name = 'Emlo_' . date('Ymdhis') . '.csv';
            $uploadPath = storage_path('app' . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR .'csvs');
            $csvFilePath = $uploadPath . DIRECTORY_SEPARATOR . $csv_name;
             
            // Make sure directory exists
            if (!File::exists($uploadPath)) {
                try {
                    File::makeDirectory($uploadPath, 0755, true);
                } catch (Exception $e) {
                    return ['success' => false, 'error' => 'Failed to create directory for CSV: ' . $e->getMessage()];
                }
            }

            // Try to create the CSV file
            if (!File::put($csvFilePath, $csvHeadersWRows)) {
                return ['success' => false, 'error' => 'Failed to write CSV file'];
            }

            Log::info('CSV file created successfully: ' . $csv_name);

            return ['success' => true, 'csvFilePath' => $csvFilePath];
        }

        return ['success' => false, 'error' => 'Emotion data not valid when creating CSV'];

    }

    private function validateEmotionsInput($emotionsData)
    {
        if (!empty($emotionsData?->response?->data?->segments?->data)){
            return true;
        }

        return false;
    }

    private function calculateRowsAndAppendToCSV($emotionData, $csvHeaders)
    {
        $segments = 0;
        $segmentNames = EmloDatabaseLoader::getSegments();
        $zeroValuedSegmentNames = EmloDatabaseLoader::getZeroValuedSegments();
        $allRowsData = [];

        foreach ($emotionData->response->data->segments->data as $segment_index => $row) {
            $data = [];
            
            // Step 1: Create an array of the segment field names from the database
            $simpleFieldNames = [];
            foreach ($segmentNames as $index => $fieldNameObj) {                    
                try {
                    $jsonStr = json_encode($fieldNameObj);
                    $decoded = json_decode($jsonStr, true);
                    $simpleFieldNames[$index] = $decoded['name'] ?? ('field_' . $index);
                } catch (Exception $e) {
                    $simpleFieldNames[$index] = 'field_' . $index;
                }
            }
            $this_row_data = '';
            
            // Step 2: Store all data of the segment into the data array
            foreach ($simpleFieldNames as $index => $fieldName) {                   
                if (isset($row[$index])) {
                    $value = $row[$index];
                    
                    // Handle non-scalar values
                    if (!is_scalar($value) && $value !== null) {
                        $value = json_encode($value);
                    }
                    
                    // Assign value to data array
                    $data[$fieldName] = $value;
                } else {
                    $data[$fieldName] = 0;
                }
            }
        
            // Handle special case for oz123
            $data['oz123'] = @$row[11] . '/' . @$row[12] . '/' . @$row[13];
        
            // Initialize zero-value fields
            foreach ($zeroValuedSegmentNames as $fieldName) {
                if (is_array($fieldName) && isset($fieldName['name'])) {
                    $fieldName = $fieldName['name'];
                } elseif (is_object($fieldName) && isset($fieldName->name)) {
                    $fieldName = $fieldName->name;
                }
                $data[$fieldName] = 0;
            }
        
            // Calculate derived values - check if riskOZ exists first
            if (isset($data['riskOZ'])) {
                $data['nmsSeg_selfHonesty'] = ($data['riskOZ'] > 100) ? 1 : (100 - $data['riskOZ']);
            }
        
            // Store the data for later averaging
            $allRowsData[] = $data;
        
            // CSV work ahead
            $this_row_data = $this->populateCSVRow($data);
        
            // Add the row data to our main CSV with a newline
            $csvHeaders .= $this_row_data . "\r\n";
            
            $segments++;
        }
        $csvHeadersWRows = $csvHeaders;
        return ['allRowsData' => $allRowsData, 'csvHeadersWRows' => $csvHeadersWRows];
    }

    private function getCsvHeaders()
    {
        $start = '';

        // Order matches database schema exactly
        $main_headers = '"Seg#","Channel","Start Pos (Sec.)","End Pos (Sec.)","Valid Segment","Topic","Question",' .
                '"Online LVA","Risk1","Risk2","RiskOZ","OZ1","OZ2","OZ3",' .
                '"Energy","Joy","Sad","Aggression","Stress","Uncertainty","Excitement",' .
                '"Uneasy","Concentration","Anticipation","Hesitation","Emo.Balance","Emo.EnergyBalance",' .
                '"Mental Effort","Imagination","Arousal","Overall.Cogn.Activ","EmoCognRatio","ExtremeEmotion",' .
                '"Atmosphere","CognitiveHighLowBalance","Voice Energy","Dissatisfied",' .
                '"LVA Global Stress","LVA Emo Stress","LVA Cogn Stress","LVA Energ Stress",' .
                '"LVA Mental Effort","LVA SOS STRESS",' .
                '"EmotionPlayerEnergy","EmotionPlayerJoy","EmotionPlayerSad","EmotionPlayerAggression","EmotionPlayerStress",' .
                '"EmotionPlayerRisk","FinalRiskLevel","EDPEnergetic","EDPPassionate","EDPEmotional",' .
                '"EDPUneasy","EDPStressful","EDPThoughtful","EDPConfident","EDPConcentrated",' .
                '"EDPAnticipation","EDPHesitation","CallPriority","CallPriorityAgent","CallDistressPriority",' .
                '"VOL1","VOL2",' .
                '"JQcl","SOS","AVJ","CHL","Fant","Fcen","Fflic","Fmain","FmainPos","Fq","FsubCog","FsubEmo","Fx","JQ","LJ","MaxVolAmp","sampleSize",' .
                '"P1","P2","P3","SPJ","SPJhl","SPJll","SPJsh","SPJsl","SPT","SPST","SPBT",' .
                '"SPBth","SPBtl","SPSth","SPStl","SPBtl_DIF","SPBth_DIF","lJQ","mJQ","hJQ","SPJsav",' .
                '"SPJlav","intCHL","SPTJtot","SPJdist","SPJcomp","JHLratio","nCHL","CHLdif",' .
                '"CCCHL","sptBdiff","HASv","AVJcl","cPor","feelGPT","GPTCommand","offlineLVA-value",' . // Note the order: cPor, feelGPT, GPTCommand BEFORE aF fields
                '"offlineLVA-riskStress","offlineLVA-riskProbability","offlineLVA-emotionStress","offlineLVA-cognitiveStress",' .
                '"offlineLVA-globalStress","offlineLVA-frgStress","offlineLVA-subjectiveEffortLevel","offlineLVA-deceptionPatterns",' .
                '"lVARiskStress","offline_lva","iThink","aF1","aF2","aF3","aF4","aF5","aF6","aF7","aF8","aF9","aF10"';

        $newline = "\r\n";

        return $start . $main_headers . $newline;
    }
    private function populateCSVRow($data)
    {
        // Extract values in exact database order
        $segment_no = $data['index'] ?? 0;
        $channel = $data['channel'] ?? '';
        $start_pos_sec = $data['startPosSec'] ?? 0;
        $end_pos_sec = $data['endPosSec'] ?? 0;
        $validSegment = $data['validSegment'] ?? 0;
        $topic = $data['topics'] ?? '';
        $question = $data['question'] ?? '';
        $online_lva = $data['onlineLVA'] ?? 0;
        $risk1 = $data['risk1'] ?? 0;
        $risk2 = $data['risk2'] ?? 0;
        $riskOZ = $data['riskOZ'] ?? 0;
        
        // OZ fields - using calculated fields for display
        $oz1 = $data['oz1'] ?? 0;
        $oz2 = $data['oz2'] ?? 0;
        $oz3 = $data['oz3'] ?? 0;
        
        // Continue with fields in exact database order
        $energy = $data['energy'] ?? 0;
        $joy = $data['joy'] ?? 0;
        $sad = $data['sad'] ?? 0;
        $aggression = $data['aggression'] ?? 0;
        $stress = $data['stress'] ?? 0;
        $uncertainty = $data['uncertainty'] ?? 0;
        $excitement = $data['excitement'] ?? 0;
        $uneasy = $data['uneasy'] ?? 0;
        $concentration = $data['concentration'] ?? 0;
        $anticipation = $data['anticipation'] ?? 0;
        $hesitation = $data['hesitation'] ?? 0;
        $emotionBalance = $data['emotionBalance'] ?? 0;
        $emotionEnergyBalance = $data['emotionEnergyBalance'] ?? 0;
        $mentalEffort = $data['mentalEffort'] ?? 0;
        $imagination = $data['imagination'] ?? 0;
        $arousal = $data['arousal'] ?? 0;
        $overallCognitiveActivity = $data['overallCognitiveActivity'] ?? 0;
        $emotionCognitiveRatio = $data['emotionCognitiveRatio'] ?? 0;
        $extremeEmotion = $data['extremeEmotion'] ?? 0;
        $atmosphere = $data['atmosphere'] ?? 0;
        $cognitiveHighLowBalance = $data['cognitiveHighLowBalance'] ?? 0;
        $voiceEnergy = $data['voiceEnergy'] ?? 0;
        $dissatisfied = $data['dissatisfied'] ?? 0;
        $LVAGlobalStress = $data['LVA-GlobalStress'] ?? 0;
        $LVAEmotionStress = $data['LVA-EmotionStress'] ?? 0;
        $LVACognitiveStress = $data['LVA-CognitiveStress'] ?? 0;
        $LVAEnergyStress = $data['LVA-EnergyStress'] ?? 0;
        $LVAMentalEffort = $data['LVA-MentalEffort'] ?? 0;
        $LVASOSStress = $data['LVA-SOSStress'] ?? 0;
        $EmotionPlayerEnergy = $data['EmotionPlayer-Energy'] ?? 0;
        $EmotionPlayerJoy = $data['EmotionPlayer-Joy'] ?? 0;
        $EmotionPlayerSad = $data['EmotionPlayer-Sad'] ?? 0;
        $EmotionPlayerAggression = $data['EmotionPlayer-Aggression'] ?? 0;
        $EmotionPlayerStress = $data['EmotionPlayer-Stress'] ?? 0;
        $EmotionPlayerRisk = $data['EmotionPlayer-Risk'] ?? 0;
        $finalRiskLevel = $data['finalRiskLevel'] ?? 0;
        $EDPEnergetic = $data['EDP-Energetic'] ?? 0;
        $EDPPassionate = $data['EDP-Passionate'] ?? 0;
        $EDPEmotional = $data['EDP-Emotional'] ?? 0;
        $EDPUneasy = $data['EDP-Uneasy'] ?? 0;
        $EDPStressful = $data['EDP-Stressful'] ?? 0;
        $EDPThoughtful = $data['EDP-Thoughtful'] ?? 0;
        $EDPConfident = $data['EDP-Confident'] ?? 0;
        $EDPConcentrated = $data['EDP-Concentrated'] ?? 0;
        $EDPAnticipation = $data['EDP-Anticipation'] ?? 0;
        $EDPHesitation = $data['EDP-Hesitation'] ?? 0;
        $callPriority = $data['callPriority'] ?? 0;
        $callPriorityAgent = $data['callPriorityAgent'] ?? 0;
        $callDistressPriority = $data['callDistressPriority'] ?? 0;
        $VOL1 = $data['VOL1'] ?? 0;
        $VOL2 = $data['VOL2'] ?? 0;
        $JQcl = $data['JQcl'] ?? 0;
        $SOS = $data['SOS'] ?? 0;
        $AVJ = $data['AVJ'] ?? 0;
        $CHL = $data['CHL'] ?? 0;
        $Fant = $data['Fant'] ?? 0;
        $Fcen = $data['Fcen'] ?? 0;
        $Fflic = $data['Fflic'] ?? 0;
        $Fmain = $data['Fmain'] ?? 0;
        $FmainPos = $data['FmainPos'] ?? 0;
        $Fq = $data['Fq'] ?? 0;
        $FsubCog = $data['FsubCog'] ?? 0;
        $FsubEmo = $data['FsubEmo'] ?? 0;
        $Fx = $data['Fx'] ?? 0;
        $JQ = $data['JQ'] ?? 0;
        $LJ = $data['LJ'] ?? 0;
        $MaxVolAmp = $data['MaxVolAmp'] ?? 0;
        $sampleSize = $data['sampleSize'] ?? 0;
        $P1 = $data['P1'] ?? 0;
        $P2 = $data['P2'] ?? 0;
        $P3 = $data['P3'] ?? 0;
        $SPJ = $data['SPJ'] ?? 0;
        $SPJhl = $data['SPJhl'] ?? 0;
        $SPJll = $data['SPJll'] ?? 0;
        $SPJsh = $data['SPJsh'] ?? 0;
        $SPJsl = $data['SPJsl'] ?? 0;
        $SPT = $data['SPT'] ?? 0;
        $SPST = $data['SPST'] ?? 0;
        $SPBT = $data['SPBT'] ?? 0;
        $SPBth = $data['SPBth'] ?? 0;
        $SPBtl = $data['SPBtl'] ?? 0;
        $SPSth = $data['SPSth'] ?? 0;
        $SPStl = $data['SPStl'] ?? 0;
        $SPBtl_DIF = $data['SPBtl_DIF'] ?? 0;
        $SPBth_DIF = $data['SPBth_DIF'] ?? 0;
        $lJQ = $data['lJQ'] ?? 0;
        $mJQ = $data['mJQ'] ?? 0;
        $hJQ = $data['hJQ'] ?? 0;
        $SPJsav = $data['SPJsav'] ?? 0;
        $SPJlav = $data['SPJlav'] ?? 0;
        $intCHL = $data['intCHL'] ?? 0;
        $SPTJtot = $data['SPTJtot'] ?? 0;
        $SPJdist = $data['SPJdist'] ?? 0;
        $SPJcomp = $data['SPJcomp'] ?? 0;
        $JHLratio = $data['JHLratio'] ?? 0;
        $nCHL = $data['nCHL'] ?? 0;
        $CHLdif = $data['CHLdif'] ?? 0;
        $CCCHL = $data['CCCHL'] ?? 0;
        $sptBdiff = $data['sptBdiff'] ?? 0;
        $HASv = $data['HASv'] ?? 0;
        $AVJcl = $data['AVJcl'] ?? 0;
        
        // Important: these fields come BEFORE aF fields in the database
        $cPor = $data['cPor'] ?? 0;
        $feelGPT = $data['feelGPT'] ?? '';
        $GPTCommand = $data['GPTCommand'] ?? '';
        
        $offlineLVAvalue = $data['offlineLVA-value'] ?? 0;
        $offlineLVAriskStress = $data['offlineLVA-riskStress'] ?? 0;
        $offlineLVAriskProbability = $data['offlineLVA-riskProbability'] ?? 0;
        $offlineLVAemotionStress = $data['offlineLVA-emotionStress'] ?? 0;
        $offlineLVAcognitiveStress = $data['offlineLVA-cognitiveStress'] ?? 0;
        $offlineLVAglobalStress = $data['offlineLVA-globalStress'] ?? 0;
        $offlineLVAfrgStress = $data['offlineLVA-frgStress'] ?? 0;
        $offlineLVAsubjectiveEffortLevel = $data['offlineLVA-subjectiveEffortLevel'] ?? 0;
        $offlineLVAdeceptionPatterns = $data['offlineLVA-deceptionPatterns'] ?? 0;
        
        $lVARiskStress = $data['lVARiskStress'] ?? 0;
        $offline_lva = $data['offline_lva'] ?? 0;
        $iThink = $data['iThink'] ?? 0;
        
        // aF fields come at the end in the database
        $aF1 = $data['aF1'] ?? 0;
        $aF2 = $data['aF2'] ?? 0;
        $aF3 = $data['aF3'] ?? 0;
        $aF4 = $data['aF4'] ?? 0;
        $aF5 = $data['aF5'] ?? 0;
        $aF6 = $data['aF6'] ?? 0;
        $aF7 = $data['aF7'] ?? 0;
        $aF8 = $data['aF8'] ?? 0;
        $aF9 = $data['aF9'] ?? 0;
        $aF10 = $data['aF10'] ?? 0;

        // Build the CSV row in the exact same order as the headers and database
        $this_row_data = '"' . $segment_no . '","' . $channel . '","' . 
            $start_pos_sec . '","' . $end_pos_sec . '","' . $validSegment . '","' . $topic . '","' . $question . '","' .
            $online_lva . '","' . $risk1 . '","' . $risk2 . '","' . $riskOZ . '","' .
            $oz1 . '","' . $oz2 . '","' . $oz3 . '","' .
            $energy . '","' . $joy . '","' . $sad . '","' . $aggression . '","' . 
            $stress . '","' . $uncertainty . '","' . $excitement . '","' .
            $uneasy . '","' . $concentration . '","' . $anticipation . '","' . $hesitation . '","' . 
            $emotionBalance . '","' . $emotionEnergyBalance . '","' . $mentalEffort . '","' .
            $imagination . '","' . $arousal . '","' . $overallCognitiveActivity . '","' . 
            $emotionCognitiveRatio . '","' . $extremeEmotion . '","' . $atmosphere . '","' .
            $cognitiveHighLowBalance . '","' . $voiceEnergy . '","' . $dissatisfied . '","' .
            $LVAGlobalStress . '","' . $LVAEmotionStress . '","' . $LVACognitiveStress . '","' . $LVAEnergyStress . '","' .
            $LVAMentalEffort . '","' . $LVASOSStress . '","' . $EmotionPlayerEnergy . '","' .
            $EmotionPlayerJoy . '","' . $EmotionPlayerSad . '","' . $EmotionPlayerAggression . '","' . $EmotionPlayerStress . '","' .
            $EmotionPlayerRisk . '","' . $finalRiskLevel . '","' . $EDPEnergetic . '","' . $EDPPassionate . '","' .
            $EDPEmotional . '","' . $EDPUneasy . '","' . $EDPStressful . '","' . $EDPThoughtful . '","' .
            $EDPConfident . '","' . $EDPConcentrated . '","' . $EDPAnticipation . '","' . $EDPHesitation . '","' .
            $callPriority . '","' . $callPriorityAgent . '","' . $callDistressPriority . '","' . $VOL1 . '","' . $VOL2 . '","' . 
            $JQcl . '","' . $SOS . '","' . $AVJ . '","' . $CHL . '","' . $Fant . '","' .
            $Fcen . '","' . $Fflic . '","' . $Fmain . '","' . $FmainPos . '","' . $Fq . '","' .
            $FsubCog . '","' . $FsubEmo . '","' . $Fx . '","' . $JQ . '","' . $LJ . '","' . $MaxVolAmp . '","' . $sampleSize . '","' .
            $P1 . '","' . $P2 . '","' . $P3 . '","' . $SPJ . '","' . $SPJhl . '","' .
            $SPJll . '","' . $SPJsh . '","' . $SPJsl . '","' . $SPT . '","' . $SPST . '","' . $SPBT . '","' .
            $SPBth . '","' . $SPBtl . '","' . $SPSth . '","' . $SPStl . '","' . $SPBtl_DIF . '","' .
            $SPBth_DIF . '","' . $lJQ . '","' . $mJQ . '","' . $hJQ . '","' . $SPJsav . '","' .
            $SPJlav . '","' . $intCHL . '","' . $SPTJtot . '","' .
            $SPJdist . '","' . $SPJcomp . '","' . $JHLratio . '","' . $nCHL . '","' . $CHLdif . '","' .
            $CCCHL . '","' . $sptBdiff . '","' . $HASv . '","' . $AVJcl . '","' .
            $cPor . '","' . $feelGPT . '","' . $GPTCommand . '","' . $offlineLVAvalue . '","' . // Note correct order here
            $offlineLVAriskStress . '","' . $offlineLVAriskProbability . '","' . $offlineLVAemotionStress . '","' . $offlineLVAcognitiveStress . '","' .
            $offlineLVAglobalStress . '","' . $offlineLVAfrgStress . '","' . $offlineLVAsubjectiveEffortLevel . '","' . $offlineLVAdeceptionPatterns . '","' .
            $lVARiskStress . '","' . $offline_lva . '","' . $iThink . '","' .
            $aF1 . '","' . $aF2 . '","' . $aF3 . '","' . $aF4 . '","' . $aF5 . '","' . $aF6 . '","' .
            $aF7 . '","' . $aF8 . '","' . $aF9 . '","' . $aF10 . '"';
        
        return $this_row_data;
    }

    private function calculateAveragesAndAppendToCSV($allRowsData, &$csvHeadersWRows)
    {
        // Initialize averages array
        $averages = [];
        
        // Get all field keys from the first row
        $fields = array_keys($allRowsData[0]);
        
        // Fields to exclude from averaging
        $excludeFields = [
        'index',
        'channel',
        'startPosSec',
        'endPosSec',
        'validSegment',
        'topics',
        'question',
        'feelGPT',
        'GPTCommand'];

        // Initialize the average values
        foreach ($fields as $field) {
            $averages[$field] = '';  // Default to empty string
        }
        
        // Set a label for the first column
        $firstField = reset($fields);
        $averages[$firstField] = 'AVERAGE';
        
        // Sum up all numeric values
        $numericFields = [];
        $rowCount = count($allRowsData);
        
        // First determine which fields are numeric and not excluded
        foreach ($fields as $field) {
            // Skip excluded fields
            if (in_array($field, $excludeFields)) {
                continue;
            }
            
            $isNumeric = false;
            
            // Check each row to see if this field contains numeric values
            foreach ($allRowsData as $row) {
                if (isset($row[$field]) && is_numeric($row[$field])) {
                    $isNumeric = true;
                    break;
                }
            }
            
            if ($isNumeric) {
                $numericFields[] = $field;
                $averages[$field] = 0;  // Initialize numeric fields to 0
            }
        }
        
        // Calculate sums for numeric fields
        foreach ($allRowsData as $row) {
            foreach ($numericFields as $field) {
                if (isset($row[$field]) && is_numeric($row[$field])) {
                    $averages[$field] += (float)$row[$field];
                }
            }
        }
        
        // Calculate averages
        foreach ($numericFields as $field) {
            if ($rowCount > 0) {
                $averages[$field] = round($averages[$field] / $rowCount, 2);
            }
        }
        
        // Format average row as CSV and add to output
        $averages_row = $this->populateCSVRow($averages);
        $csvHeadersWRows .= $averages_row . "\r\n";

        return $csvHeadersWRows;
    }
}