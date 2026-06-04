const { DataTypes } = require('sequelize');
const sequelize = require('../config/database'); 

const MaintenanceBhp = sequelize.define('MaintenanceBhp', {
    bhp_id: {
      type: DataTypes.INTEGER, 
      allowNull: true
    },
    jumlah_digunakan: {
      type: DataTypes.STRING, 
      allowNull: true
    },
    maintenance_id: {
      type: DataTypes.INTEGER, 
      allowNull: true
    },
    barang_id: {
      type: DataTypes.INTEGER, 
      allowNull: true
    }
}, {
  tableName: 'maintenance_bhp',
  timestamps: false 
});

module.exports = MaintenanceBhp;
