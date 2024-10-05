<div class="navbar-top" id="navbar-top">
  <a class="btn grn" onclick="doGoodLift();">Good Lift</a>
  <a class="btn red" onclick="doNoLift();">No Lift</a>
  <a class="btn" onclick="doBreakTimer();">Break...</a>
  <!--  <a class="btn" onclick="changeSession();startOfGroup();">Change Session</a> -->
  <a class="btn" onclick="setupPopup=window.open('./sheetsetup.php?popup=1','setupPopup','status=no,location=no,toolbar=no,menubar=no,width=600,height=800,left=0,top=0')">Setup...</a>
  <select id="selectlift" class="btn" style="font-family:inherit;font-size:inherit;" onclick="setActiveLift(this.value);">
    <option value="" selected disabled hidden>Set Active Lift</option>
    <option value="wei">Weigh In</option>
    <option value="sq1">Squat 1</option>
    <option value="sq2">Squat 2</option>
    <option value="sq3">Squat 3</option>
    <option value="bp1">Benchpress 1</option>
    <option value="bp2">Benchpress 2</option>
    <option value="bp3">Benchpress 3</option>
    <option value="dl1">Deadlift 1</option>
    <option value="dl2">Deadlift 2</option>
    <option value="dl3">Deadlift 3</option>
    <option value="res">Results</option>
  </select>
  <select id="selectgp" class="btn" style="font-family:inherit;font-size:inherit;" onchange="setActiveGroup(this.value);">
  </select>
</div>

<script>
</script>
