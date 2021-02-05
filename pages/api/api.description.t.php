<h1>
    <?= t('api.title') ?>  
</h1>

<h2>
    <?= t('competition.title') ?>
</h2>
<ul>
    <li>
        /api/v0/competitions/{%competition_id}
    </li>
    <li>
        /api/v0/competitions/{%competition_id}/registrations
    </li>
    <li>
        /api/v0/competitions/{%competition_id}/results
    </li>
</ul>

<h2>
    <?= t('team.title') ?>
</h2>
<ul>
    <li>
        <a href='%i/api/v0/team'>
            /api/v0/team
        </a>
    </li>
</ul>    

<h2>
    <?= t('records.title') ?>
</h2>
<ul>
    <li>
        <a href='%i/api/v0/records'>
            /api/v0/records
        </a>
    </li>
</ul>

<h2>
    <?= t('persons.title') ?>
</h2>
<ul>
    <li>
        /api/v0/persons/{%wca_id}
    </li>
</ul>