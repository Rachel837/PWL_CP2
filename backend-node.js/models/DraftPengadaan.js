const { DataTypes } = require('sequelize');
const sequelize = require('../config/database'); 

const DraftPengadaan = sequelize.define('DraftPengadaan', {
    tahun: {
      type: DataTypes.STRING, 
      allowNull: true
    },
    status: {
      type: DataTypes.STRING, 
      allowNull: true
    },
    catatan: {
      type: DataTypes.STRING, 
      allowNull: true
    },
    users_id: {
      type: DataTypes.STRING, 
      allowNull: true
    }
}, {
  tableName: 'draft_pengadaan',
  timestamps: false 
});

module.exports = DraftPengadaan;
