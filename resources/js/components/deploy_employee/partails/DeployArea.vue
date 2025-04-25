<template>
  <div>
    <el-date-picker
      v-model="date"
      @change="changeDate"
      type="date"
      style="margin-bottom:10px"
      :clearable="false"
      placeholder="Pick a day"
    ></el-date-picker>
    <el-button @click="refresh"><i class="fas fa-retweet"></i></el-button>
    <el-select v-model="selectedArea" @change="selectArea">
      <el-option
        v-for="(area, index) in deploy"
        :key="index"
        :label="getAreaLabel(area)"
        :value="area.coordinates"
        v-html="getAreaLabel(area)"
      ></el-option>
    </el-select>
    <el-button @click="centerMapOnClick">Center Map</el-button>
    <div style="height: 600px; width: 100%">
      <l-map
        ref="map"
        :zoom="zoom"
        :center="center"
        :options="mapOptions"
        style="height: 100%"
      >
        <l-polygon
          v-for="(area, index) in deploy"
          :key="index"
          :color="area.color"
          :lat-lngs="area.coordinates"
          @click="centerMap(area)"
        >
          <l-popup :ref="`${area.id}-popup`" :openPopup="false">
            <h4>{{ area.name }}</h4>
            <div v-if="area.deploy_team">
              <div v-for="(dep, index2) in area.deploy_team" :key="index2">
                <span style="padding:0; margin:0; margin-bottom:0;">
                  TASK: {{ dep.task.task.name }}
                </span>
                <div style="width: 100%">
                  <table class="table table-sm table-bordered table-striped">
                    <thead>
                      <th>Members</th>
                    </thead>
                    <tbody>
                      <tr
                        v-for="member in dep.daily_operation.daily_operation_team.daily_operation_team_member"
                        :key="member.id"
                      >
                        <td v-if="member.employee.position">
                          {{ member.employee.lastname }},
                          {{ member.employee.firstname }} -
                          ({{ member.employee.position }})
                        </td>
                        <td v-else>
                          {{ member.employee.lastname }},
                          {{ member.employee.firstname }}
                        </td>
                      </tr>
                      <td>
                        coordinates: {{ area.coordinates[0][0] }},
                        {{ area.coordinates[0][1] }}
                      </td>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </l-popup>
        </l-polygon>

        <l-tile-layer :url="url" :attribution="attribution" />
      </l-map>
    </div>
  </div>
</template>

<script>
import moment from "moment";
import { latLng } from "leaflet";
import {
  LMap,
  LTileLayer,
  LMarker,
  LPopup,
  LTooltip,
  LCircle,
  LIcon,
  LPolygon,
} from "vue2-leaflet";

export default {
  name: "DeployArea",
  components: {
    LMap,
    LTileLayer,
    LMarker,
    LPopup,
    LTooltip,
    LCircle,
    LIcon,
    LPolygon,
  },
  data() {
    return {
      date: "",
      search: "",
      deploy: [],
      zoom: 15,
      center: { lat: 7.562480049203623, lng: 125.67993587531065 },
      url:
        "https://tile.thunderforest.com/cycle/{z}/{x}/{y}.png?apikey=cac1c63e746741a79462820881e7f2c6",
      attribution: "Farm Management",
      showParagraph: true,
      mapOptions: {
        zoomSnap: 0.5,
      },
      coordinates: {},
      selectedArea: null,
      selectedAreaMarker: null,
    };
  },
  mounted() {
    this.getLocation();
    this.center = { lat: 7.562480049203623, lng: 125.67993587531065 };
  },
  created() {
    this.date = new Date();
    this.getDeploy();
    this.$EventDispatcher.listen("DELETE_DEPLOY", (data) => {
      this.deploy.filter((dep) => dep.id != data);
    });

    this.$EventDispatcher.listen("NEW_DEPLOY_TEAM", (data) => {
      this.deploy.push(data);
    });
  },
  computed: {
    getFirstArea() {
      if (this.deploy.length > 0) {
        if (this.deploy[0].coordinates.length > 0) {
          return latLng(
            this.deploy[0].coordinates[0][0],
            this.deploy[0].coordinates[0][1]
          );
        }
        return latLng(this.coordinates.lat, this.coordinates.lng);
      }
      return latLng(this.coordinates.lat, this.coordinates.lng);
    },
    deployData() {
      let deplot = this.deploy.map((dep) => {
        let arrayData = [];
        return dep;
      });
    },
  },
  methods: {

    centerMapOnClick() {
    const newCenter = { lat: 7.548270766165667, lng: 125.67040866897632 };
   
    this.center = newCenter;
  },
    getAreaLabel(area) {
      const hasDeploy = this.checkIfAreaHasDeploy(area);

      // If the area has a deploy, add "(Deploy)" with blue color to the label, otherwise, just return the area name
      return `${area.name}${hasDeploy ? '<span style="color: #2471A3;">(Deploy)</span>' : ''}`;
    },

    checkIfAreaHasDeploy(area) {
      const selectedDate = moment(this.date).format("YYYY-MM-DD");

      const hasDeploy = area.deploy_team.some((dep) => {
        const deployDate = moment(dep.date).format("YYYY-MM-DD");
        return deployDate === selectedDate;
      });

      return hasDeploy;
    },

    updateSelectedAreaMarker(area) {
      if (this.selectedAreaMarker) {
        this.selectedAreaMarker.removeFrom(this.$refs.map.mapObject); // Remove the old marker from the map
      }

      const [lat, lng] = area.coordinates[0]; // Assuming your coordinates are in [lat, lng] format

      // Create a new marker icon and add it to the map
      this.selectedAreaMarker = L.marker([lat, lng], {
        icon: L.icon({
          iconUrl: "/img/area.png", // Replace with your marker icon image URL
          iconSize: [32, 32], // Adjust the size as needed
          iconAnchor: [16, 32], // Adjust the anchor point as needed
        }),
      }).addTo(this.$refs.map.mapObject);
    },
    centerMap(area) {
      const [lat, lng] = area.coordinates[0]; // Assuming your coordinates are in [lat, lng] format
      this.center = latLng(lat, lng); // Update the center with the clicked area's coordinates
    },

    selectArea() {
      if (this.selectedArea) {
        const selectedArea = this.deploy.find(
          (area) => area.coordinates === this.selectedArea
        );
        if (selectedArea) {
          this.updateSelectedAreaMarker(selectedArea); // Update the selected area marker icon
          this.centerMap(selectedArea); // Center the map on the selected area
        }
      }
    },
    async getDeploy() {
      try {
        this.date = this.$df.formatDate(this.date, "YYYY-MM-DD");

        let params = {
          search: this.search,
          date: this.date,
        };

        this.loading = true;
        const res = await this.$API.Deploy.getDeployByArea(params);
        this.deploy = res.data;
        this.loading = false;
      } catch (error) {
        console.log(error);
      }
    },
    changeDate(value) {
      this.date = value;
      this.getDeploy();
    },
    async getLocation() {
      try {
        const coordinates = await this.$getLocation({
          enableHighAccuracy: true,
        });
        this.center = latLng(coordinates.lat, coordinates.lng);
        this.coordinates = coordinates;
      } catch (error) {
        alert("Turn on your location");
        window.location.reload();
      }
    },
    refresh() {
      this.getDeploy();
    },
    showPopup(area) {
      if (area) {
        const popup = this.$refs[`${area.id}-popup`];
        if (popup) {
          popup.openPopup = true; // Use property to open the popup
        }
      }
    },
    hidePopup(area) {
      if (area) {
        const popup = this.$refs[`${area.id}-popup`];
        if (popup) {
          popup.openPopup = false; // Use property to close the popup
        }
      }
    },
  },
};
</script>
