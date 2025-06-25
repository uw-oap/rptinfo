<div class="card">
    <div class="card-body">
        <h4 class="card-title">Candidate info</h4>
        <p class="card-subtitle mb-2 text-muted">
            Please review the candidate's Workday information below. If any data is incorrect, make the
            change in Workday. Once updated, return to this page to initiate the case. <em>Do not</em>
            initiate a case with incorrect information.</p>
        <dl class="ptinfo-list">
            <dt>Employee ID</dt>
            <dd><?= $case_obj->EmployeeID; ?> ></dd>
            <dt>Name</dt>
            <dd><?= $case_obj->LegalName; ?></dd>
            <dt>Appointment type</dt>
            <dd><?= $case_obj->AppointmentType; ?></dd>
            <dt>S/C/C</dt>
            <dd><?= $case_obj->LevelOneName; ?></dd>
            <dt>Appointing unit</dt>
            <dd><?= $case_obj->UnitName; ?></dd>
            <dt>Current rank</dt>
            <dd><?= $case_obj->CurrentRankName; ?></dd>
            <dt>Track type</dt>
            <dd><?= $case_obj->TrackTypeName; ?></dd>
            <?php if (count($case_obj->OtherAppointments)) : ?>
                <dt>Other appointments</dt>
                <dd><ul>
                <?php foreach ($case_obj->OtherAppointments as $appointment) : ?>
                    <li><?= $appointment->RankName . ' in ' . $appointment->UnitName . ' ('
                            . $appointment->AppointmentType . ')'; ?></li>
                <?php endforeach; ?>
                </ul></dd>
            <?php endif; ?>
            </dl>
        </div> <!-- card body -->
    </div> <!-- card -->
