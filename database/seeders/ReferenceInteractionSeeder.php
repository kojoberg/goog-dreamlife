<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReferenceInteractionSeeder extends Seeder
{
    public function run(): void
    {
        $interactions = [
            // --- WARFARIN INTERACTIONS ---
            ['drug_a_name' => 'Warfarin', 'drug_b_name' => 'Aspirin', 'severity' => 'severe', 'description' => 'Greatly increased risk of bleeding. Antiplatelet effect additive to anticoagulant effect.'],
            ['drug_a_name' => 'Warfarin', 'drug_b_name' => 'Ibuprofen', 'severity' => 'severe', 'description' => 'Increased bleeding risk. NSAIDs irritate gastric mucosa and inhibit platelets.'],
            ['drug_a_name' => 'Warfarin', 'drug_b_name' => 'Naproxen', 'severity' => 'severe', 'description' => 'Significant bleeding risk. Avoid concurrent use.'],
            ['drug_a_name' => 'Warfarin', 'drug_b_name' => 'Diclofenac', 'severity' => 'severe', 'description' => 'High risk of GI bleeding.'],
            ['drug_a_name' => 'Warfarin', 'drug_b_name' => 'Ciprofloxacin', 'severity' => 'moderate', 'description' => 'Antibiotic may enhance anticoagulant effect of Warfarin. Monitor INR.'],
            ['drug_a_name' => 'Warfarin', 'drug_b_name' => 'Azithromycin', 'severity' => 'moderate', 'description' => 'May increase Warfarin effect/INR.'],
            ['drug_a_name' => 'Warfarin', 'drug_b_name' => 'Erythromycin', 'severity' => 'moderate', 'description' => 'Inhibits Warfarin metabolism, increasing bleeding risk.'],
            ['drug_a_name' => 'Warfarin', 'drug_b_name' => 'Metronidazole', 'severity' => 'severe', 'description' => 'Significant increase in INR. Avoid or reduce Warfarin dose by 30-50%.'],
            ['drug_a_name' => 'Warfarin', 'drug_b_name' => 'Fluconazole', 'severity' => 'moderate', 'description' => 'Inhibits CYP2C9, increasing Warfarin plasma levels.'],
            ['drug_a_name' => 'Warfarin', 'drug_b_name' => 'Amiodarone', 'severity' => 'severe', 'description' => 'Inhibits Warfarin metabolism. INR can rise dramatically after 1-2 weeks.'],
            ['drug_a_name' => 'Warfarin', 'drug_b_name' => 'Simvastatin', 'severity' => 'moderate', 'description' => 'May increase bleeding risk. Mechanism unclear.'],

            // --- STATIN INTERACTIONS ---
            ['drug_a_name' => 'Simvastatin', 'drug_b_name' => 'Amiodarone', 'severity' => 'severe', 'description' => 'Increased risk of rhabdomyolysis. Max Simvastatin dose 20mg.'],
            ['drug_a_name' => 'Simvastatin', 'drug_b_name' => 'Amlodipine', 'severity' => 'moderate', 'description' => 'Increased statin levels. Max Simvastatin dose 20mg.'],
            ['drug_a_name' => 'Simvastatin', 'drug_b_name' => 'Diltiazem', 'severity' => 'moderate', 'description' => 'Increased Simvastatin exposure. Max dose 10mg.'],
            ['drug_a_name' => 'Simvastatin', 'drug_b_name' => 'Verapamil', 'severity' => 'moderate', 'description' => 'Increased Simvastatin exposure. Max dose 10mg.'],
            ['drug_a_name' => 'Atorvastatin', 'drug_b_name' => 'Clarithromycin', 'severity' => 'severe', 'description' => 'Strong CYP3A4 inhibition increases Atorvastatin toxicity.'],
            ['drug_a_name' => 'Atorvastatin', 'drug_b_name' => 'Itraconazole', 'severity' => 'severe', 'description' => 'Increases Atorvastatin levels significantly.'],
            ['drug_a_name' => 'Rosuvastatin', 'drug_b_name' => 'Antacids', 'severity' => 'mild', 'description' => 'Aluminum/Magnesium antacids decrease Rosuvastatin absorption. Separate by 2 hours.'],

            // --- ACE INHIBITORS / ARBS / POTASSIUM ---
            ['drug_a_name' => 'Lisinopril', 'drug_b_name' => 'Spironolactone', 'severity' => 'moderate', 'description' => 'Risk of hyperkalemia. Monitor potassium.'],
            ['drug_a_name' => 'Lisinopril', 'drug_b_name' => 'Potassium Chloride', 'severity' => 'moderate', 'description' => 'Risk of hyperkalemia.'],
            ['drug_a_name' => 'Enalapril', 'drug_b_name' => 'Trimethoprim', 'severity' => 'moderate', 'description' => 'Combined use increases risk of hyperkalemia, especially in elderly.'],
            ['drug_a_name' => 'Losartan', 'drug_b_name' => 'Lithium', 'severity' => 'moderate', 'description' => 'May increase Lithium levels and toxicity.'],

            // --- ANTIBIOTICS ---
            ['drug_a_name' => 'Ciprofloxacin', 'drug_b_name' => 'Theophylline', 'severity' => 'severe', 'description' => 'Inhibits Theophylline metabolism, leading to seizures/toxicity.'],
            ['drug_a_name' => 'Ciprofloxacin', 'drug_b_name' => 'Tizanidine', 'severity' => 'severe', 'description' => 'CONTRAINDICATED. Hypotension and sedation risk.'],
            ['drug_a_name' => 'Doxycycline', 'drug_b_name' => 'Calcium', 'severity' => 'moderate', 'description' => 'Decreased absorption of antibiotic. Separate administration.'],
            ['drug_a_name' => 'Doxycycline', 'drug_b_name' => 'Iron', 'severity' => 'moderate', 'description' => 'Decreased absorption of antibiotic.'],
            ['drug_a_name' => 'Doxycycline', 'drug_b_name' => 'Antacids', 'severity' => 'moderate', 'description' => 'Bind to antibiotic reducing efficacy.'],

            // --- PSYCHOTROPICS / CNS ---
            ['drug_a_name' => 'Fluoxetine', 'drug_b_name' => 'Tramadol', 'severity' => 'severe', 'description' => 'Risk of Serotonin Syndrome and seizures. Effect of Tramadol reduced.'],
            ['drug_a_name' => 'Sertraline', 'drug_b_name' => 'Tramadol', 'severity' => 'severe', 'description' => 'Increased risk of Serotonin Syndrome.'],
            ['drug_a_name' => 'Fluoxetine', 'drug_b_name' => 'Dextromethorphan', 'severity' => 'moderate', 'description' => 'Risk of Serotonin Syndrome.'],
            ['drug_a_name' => 'Diazepam', 'drug_b_name' => 'Alcohol', 'severity' => 'severe', 'description' => 'Enhanced CNS depression, respiratory depression.'],
            ['drug_a_name' => 'Alprazolam', 'drug_b_name' => 'Ketoconazole', 'severity' => 'severe', 'description' => 'Increases Alprazolam levels significantly.'],

            // --- NSAIDS ---
            ['drug_a_name' => 'Ibuprofen', 'drug_b_name' => 'Methotrexate', 'severity' => 'severe', 'description' => 'Reduced excretion of Methotrexate, increasing toxicity.'],
            ['drug_a_name' => 'Naproxen', 'drug_b_name' => 'Prednisolone', 'severity' => 'moderate', 'description' => 'Increased risk of gastric ulcers.'],
            ['drug_a_name' => 'Diclofenac', 'drug_b_name' => 'Citalopram', 'severity' => 'moderate', 'description' => 'Increased bleeding risk.'],

            // --- OTHERS ---
            ['drug_a_name' => 'Sildenafil', 'drug_b_name' => 'Nitroglycerin', 'severity' => 'severe', 'description' => 'CONTRAINDICATED. Severe hypotension.'],
            ['drug_a_name' => 'Tadalafil', 'drug_b_name' => 'Nitroglycerin', 'severity' => 'severe', 'description' => 'CONTRAINDICATED. Severe hypotension.'],
            ['drug_a_name' => 'Metformin', 'drug_b_name' => 'Contrast Media', 'severity' => 'moderate', 'description' => 'Risk of lactic acidosis. Hold Metformin 48h after contrast.'],
            ['drug_a_name' => 'Levothyroxine', 'drug_b_name' => 'Calcium', 'severity' => 'moderate', 'description' => 'Decreased absorption of thyroid hormone.'],
            ['drug_a_name' => 'Levothyroxine', 'drug_b_name' => 'Iron', 'severity' => 'moderate', 'description' => 'Decreased absorption of thyroid hormone.'],
            ['drug_a_name' => 'Bisphosphonates', 'drug_b_name' => 'Calcium', 'severity' => 'moderate', 'description' => 'Must be taken at least 30 mins apart.'],
            ['drug_a_name' => 'Digoxin', 'drug_b_name' => 'Verapamil', 'severity' => 'moderate', 'description' => 'Increases Digoxin levels.'],
            // --- DIABETES INTERACTIONS ---
            ['drug_a_name' => 'Metformin', 'drug_b_name' => 'Furosemide', 'severity' => 'moderate', 'description' => 'Furosemide increases Metformin plasma concentrations. Monitor blood glucose.'],
            ['drug_a_name' => 'Metformin', 'drug_b_name' => 'Hydrochlorothiazide', 'severity' => 'moderate', 'description' => 'Thiazides can impair glucose tolerance. Metformin dose adjustment may be needed.'],
            ['drug_a_name' => 'Glimepiride', 'drug_b_name' => 'Fluconazole', 'severity' => 'severe', 'description' => 'Fluconazole inhibits metabolism of Glimepiride. Risk of severe hypoglycemia.'],
            ['drug_a_name' => 'Glipizide', 'drug_b_name' => 'Ciprofloxacin', 'severity' => 'moderate', 'description' => 'Fluoroquinolones may enhance hypoglycemic effect.'],
            ['drug_a_name' => 'Insulin', 'drug_b_name' => 'Propranolol', 'severity' => 'moderate', 'description' => 'Beta-blockers may mask symptoms of hypoglycemia (tremor, palpitations).'],

            // --- BETA BLOCKERS ---
            ['drug_a_name' => 'Atenolol', 'drug_b_name' => 'Verapamil', 'severity' => 'severe', 'description' => 'Additive negative inotropic and chronotropic effects. Risk of heart block.'],
            ['drug_a_name' => 'Propranolol', 'drug_b_name' => 'Rizatriptan', 'severity' => 'moderate', 'description' => 'Propranolol inhibits Rizatriptan metabolism. Reduce Rizatriptan dose.'],
            ['drug_a_name' => 'Carvedilol', 'drug_b_name' => 'Digoxin', 'severity' => 'moderate', 'description' => 'Increased Digoxin levels and bradycardia.'],

            // --- OPIOIDS / PAIN ---
            ['drug_a_name' => 'Codeine', 'drug_b_name' => 'Fluoxetine', 'severity' => 'moderate', 'description' => 'Fluoxetine inhibits conversion of Codeine to Morphine, reducing efficacy.'],
            ['drug_a_name' => 'Tramadol', 'drug_b_name' => 'Ondansetron', 'severity' => 'moderate', 'description' => 'Ondansetron may reduce analgesic effect of Tramadol. Risk of Serotonin Syndrome.'],
            ['drug_a_name' => 'Morphine', 'drug_b_name' => 'Gabapentin', 'severity' => 'moderate', 'description' => 'CNS depression significantly potentiated. Respiratory depression risk.'],
            ['drug_a_name' => 'Fentanyl', 'drug_b_name' => 'Erythromycin', 'severity' => 'severe', 'description' => 'CYP3A4 inhibition increases Fentanyl levels. Respiratory depression risk.'],
            ['drug_a_name' => 'Oxycodone', 'drug_b_name' => 'Diazepam', 'severity' => 'severe', 'description' => 'Profound sedation, respiratory depression, coma, and death.'],

            // --- ANTIFUNGALS ---
            ['drug_a_name' => 'Itraconazole', 'drug_b_name' => 'Omeprazole', 'severity' => 'moderate', 'description' => 'Reduced absorption of Itraconazole due to increased pH.'],
            ['drug_a_name' => 'Ketoconazole', 'drug_b_name' => 'Simvastatin', 'severity' => 'severe', 'description' => 'CONTRAINDICATED. Severe risk of rhabdomyolysis.'],
            ['drug_a_name' => 'Fluconazole', 'drug_b_name' => 'Warfarin', 'severity' => 'severe', 'description' => 'Increases reduced prothrombin time. Bleeding risk.'],

            // --- SUPPLEMENTS / HERBALS ---
            ['drug_a_name' => 'St. Johns Wort', 'drug_b_name' => 'Cyclosporine', 'severity' => 'severe', 'description' => 'Reduces Cyclosporine levels significantly. Transplant rejection risk.'],
            ['drug_a_name' => 'St. Johns Wort', 'drug_b_name' => 'Oral Contraceptives', 'severity' => 'severe', 'description' => 'Reduces efficacy of birth control pills.'],
            ['drug_a_name' => 'Ginkgo Biloba', 'drug_b_name' => 'Aspirin', 'severity' => 'moderate', 'description' => 'Additive antiplatelet effects. Bleeding risk.'],
            ['drug_a_name' => 'Ginkgo Biloba', 'drug_b_name' => 'Warfarin', 'severity' => 'severe', 'description' => 'Increased bleeding risk.'],

            // --- RESPIRATORY ---
            ['drug_a_name' => 'Theophylline', 'drug_b_name' => 'Cimetidine', 'severity' => 'severe', 'description' => 'Increases Theophylline levels/toxicity.'],
            ['drug_a_name' => 'Salbutamol', 'drug_b_name' => 'Propranolol', 'severity' => 'severe', 'description' => 'Non-selective beta-blockers antagonize bronchodilation. Risk of bronchospasm.'],
            ['drug_a_name' => 'Fluticasone', 'drug_b_name' => 'Ritonavir', 'severity' => 'severe', 'description' => 'Greatly increases Fluticasone plasma levels. Cushing syndrome risk.'],

            // --- GOUT ---
            ['drug_a_name' => 'Allopurinol', 'drug_b_name' => 'Amoxicillin', 'severity' => 'mild', 'description' => 'Increased risk of skin rash.'],
            ['drug_a_name' => 'Colchicine', 'drug_b_name' => 'Clarithromycin', 'severity' => 'severe', 'description' => 'Fatal Colchicine toxicity possible due to CYP3A4 inhibition.'],

            // --- ANTI-EPILEPTICS ---
            ['drug_a_name' => 'Phenytoin', 'drug_b_name' => 'Oral Contraceptives', 'severity' => 'severe', 'description' => 'Phenytoin induces metabolism of contraceptives. Pregnancy risk.'],
            ['drug_a_name' => 'Carbamazepine', 'drug_b_name' => 'Erythromycin', 'severity' => 'severe', 'description' => 'Increases Carbamazepine levels/toxicity.'],
            ['drug_a_name' => 'Valproate', 'drug_b_name' => 'Lamotrigine', 'severity' => 'severe', 'description' => 'Valproate inhibits Lamotrigine metabolism. Risk of Stevens-Johnson Syndrome.'],
            ['drug_a_name' => 'Topiramate', 'drug_b_name' => 'Metformin', 'severity' => 'moderate', 'description' => 'Topiramate may increase Metformin clearance.'],

            // --- MISCELLANEOUS ---
            ['drug_a_name' => 'Methotrexate', 'drug_b_name' => 'Trimethoprim', 'severity' => 'severe', 'description' => 'Bone marrow suppression (pancytopenia). Avoid combination.'],
            ['drug_a_name' => 'Levodopa', 'drug_b_name' => 'Iron', 'severity' => 'moderate', 'description' => 'Iron reduces Levodopa absorption.'],
            ['drug_a_name' => 'Bisphosphonates', 'drug_b_name' => 'PPIs', 'severity' => 'mild', 'description' => 'Possible reduced efficacy of bisphosphonate.'],
            ['drug_a_name' => 'Clopidogrel', 'drug_b_name' => 'Omeprazole', 'severity' => 'severe', 'description' => 'Omeprazole inhibits CYP2C19, reducing Clopidogrel efficacy.'],
            ['drug_a_name' => 'Tamoxifen', 'drug_b_name' => 'Fluoxetine', 'severity' => 'severe', 'description' => 'Fluoxetine inhibits formation of active Tamoxifen metabolite. Reduced efficacy.'],
            // --- HIV / ANTIVIRALS ---
            ['drug_a_name' => 'Ritonavir', 'drug_b_name' => 'Simvastatin', 'severity' => 'severe', 'description' => 'CONTRAINDICATED. Life-threatening rhabdomyolysis.'],
            ['drug_a_name' => 'Ritonavir', 'drug_b_name' => 'Amiodarone', 'severity' => 'severe', 'description' => 'Increases Amiodarone levels. Arrhythmia risk.'],
            ['drug_a_name' => 'Tenofovir', 'drug_b_name' => 'Didanosine', 'severity' => 'severe', 'description' => 'Increases Didanosine toxicity (pancreatitis, neuropathy).'],
            ['drug_a_name' => 'Atazanavir', 'drug_b_name' => 'Omeprazole', 'severity' => 'moderate', 'description' => 'PPIs reduce Atazanavir absorption significantly.'],

            // --- IMMUNOSUPPRESSANTS ---
            ['drug_a_name' => 'Tacrolimus', 'drug_b_name' => 'Erythromycin', 'severity' => 'severe', 'description' => 'Inhibits Tacrolimus metabolism. Nephrotoxicity risk.'],
            ['drug_a_name' => 'Tacrolimus', 'drug_b_name' => 'Fluconazole', 'severity' => 'moderate', 'description' => 'Increases Tacrolimus levels.'],
            ['drug_a_name' => 'Cyclosporine', 'drug_b_name' => 'Allopurinol', 'severity' => 'moderate', 'description' => 'Risk of Cyclosporine toxicity.'],

            // --- ANTIDEPRESSANTS (SSRI/SNRI/MAOI) ---
            ['drug_a_name' => 'Phenelzine', 'drug_b_name' => 'Pseudoephedrine', 'severity' => 'severe', 'description' => 'Hypertensive crisis. MAOI interaction.'],
            ['drug_a_name' => 'Selegiline', 'drug_b_name' => 'Fluoxetine', 'severity' => 'severe', 'description' => 'CONTRAINDICATED. Serotonin Syndrome risk. Wait 5 weeks after stopping Fluoxetine.'],
            ['drug_a_name' => 'Venlafaxine', 'drug_b_name' => 'Indomethacin', 'severity' => 'moderate', 'description' => 'Increased bleeding risk.'],
            ['drug_a_name' => 'Paroxetine', 'drug_b_name' => 'Tamoxifen', 'severity' => 'severe', 'description' => 'Paroxetine prevents activation of Tamoxifen. Breast cancer recurrence risk.'],
            ['drug_a_name' => 'Duloxetine', 'drug_b_name' => 'Ciprofloxacin', 'severity' => 'moderate', 'description' => 'Cipro inhibits CYP1A2, increasing Duloxetine levels.'],

            // --- ANTIPSYCHOTICS ---
            ['drug_a_name' => 'Clozapine', 'drug_b_name' => 'Ciprofloxacin', 'severity' => 'severe', 'description' => 'Increases Clozapine levels. Seizure/cardiac risk.'],
            ['drug_a_name' => 'Olanzapine', 'drug_b_name' => 'Carbamazepine', 'severity' => 'moderate', 'description' => 'Carbamazepine increases Olanzapine clearance, reducing efficacy.'],
            ['drug_a_name' => 'Quetiapine', 'drug_b_name' => 'Phenytoin', 'severity' => 'moderate', 'description' => 'Phenytoin increases Quetiapine clearance significantly.'],
            ['drug_a_name' => 'Haloperidol', 'drug_b_name' => 'Amiodarone', 'severity' => 'severe', 'description' => 'QT prolongation risk. Torsades de pointes.'],

            // --- CARDIOVASCULAR (EXTENDED) ---
            ['drug_a_name' => 'Digoxin', 'drug_b_name' => 'Clarithromycin', 'severity' => 'severe', 'description' => 'Antibiotic increases Digoxin absorption (P-gp inhibition). Digoxin toxicity.'],
            ['drug_a_name' => 'Spironolactone', 'drug_b_name' => 'Trimethoprim', 'severity' => 'severe', 'description' => 'Sudden death risk from hyperkalemia, especially in elderly.'],
            ['drug_a_name' => 'Clopidogrel', 'drug_b_name' => 'Esomeprazole', 'severity' => 'moderate', 'description' => 'Reduces Clopidogrel efficacy (CYP2C19 inhibition).'],
            ['drug_a_name' => 'Diltiazem', 'drug_b_name' => 'Ivabradine', 'severity' => 'severe', 'description' => 'Concurrent use increases risk of severe bradycardia.'],
            ['drug_a_name' => 'Sotalol', 'drug_b_name' => 'Moxifloxacin', 'severity' => 'severe', 'description' => 'QT prolongation risk.'],

            // --- ONCOLOGY ---
            ['drug_a_name' => 'Docetaxel', 'drug_b_name' => 'Ketoconazole', 'severity' => 'severe', 'description' => 'Increases Docetaxel toxicity.'],
            ['drug_a_name' => 'Erlotinib', 'drug_b_name' => 'Omeprazole', 'severity' => 'severe', 'description' => 'PPIs decrease Erlotinib absorption (pH dependent).'],

            // --- URINARY / ED ---
            ['drug_a_name' => 'Finasteride', 'drug_b_name' => 'Diltiazem', 'severity' => 'moderate', 'description' => 'Increases Finasteride levels.'],
            ['drug_a_name' => 'Tamsulosin', 'drug_b_name' => 'Cimetidine', 'severity' => 'moderate', 'description' => 'Decreases Tamsulosin clearance.'],

            // --- HERBAL / VITAMINS ---
            ['drug_a_name' => 'Calcium', 'drug_b_name' => 'Ceftriaxone', 'severity' => 'severe', 'description' => 'Risk of precipitation in lungs/kidneys (IV use). Avoid mixing.'],
            ['drug_a_name' => 'Vitamin E', 'drug_b_name' => 'Warfarin', 'severity' => 'moderate', 'description' => 'High doses of Vitamin E may enhance anticoagulant effect.'],
        ];

        foreach ($interactions as $interaction) {
            DB::table('reference_interactions')->updateOrInsert(
                [
                    'drug_a_name' => $interaction['drug_a_name'],
                    'drug_b_name' => $interaction['drug_b_name']
                ],
                [
                    'severity' => $interaction['severity'],
                    'description' => $interaction['description'],
                    'source' => 'system_seed',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
